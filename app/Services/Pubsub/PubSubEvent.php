<?php

namespace App\Services\Pubsub;

use Carbon\Carbon;
use Google\Cloud\PubSub\Message;
use Illuminate\Contracts\Container\Container;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Kainxspirits\PubSubQueue\Jobs\PubSubJob;
use Throwable;

class PubSubEvent
{
    protected Message $message;

    protected PubSubJob $job;

    public function __construct(
        protected Container $container
    ) {}

    public function handle(): void
    {
        $this->job = $this->createJob();

        event(new JobProcessing('pubsub', $this->job));

        try {
            $this->job->fire();

            event(new JobProcessed('pubsub', $this->job));

        } catch (\Exception $e) {
            $this->handleJobException($e);

        }
    }

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    protected function createJob(): PubSubJob
    {
        return new PubSubJob(
            $this->container,
            Queue::connection('pubsub'),
            $this->message,
            Config::get('queue.connections.pubsub.driver'),
            Config::get('queue.connections.pubsub.queue'),
        );
    }

    protected function handleJobException(Throwable $e)
    {
        try {
            if (! $this->job->hasFailed()) {
                if ($this->job->retryUntil() && $this->job->retryUntil() <= Carbon::now()->getTimestamp()) {
                    $this->job->fail($e);
                }

                if (! $this->job->retryUntil() && $this->job->maxTries() > 0 && $this->job->attempts() >= $this->job->maxTries()) {
                    $this->job->fail($e);
                }
            }

            event(new JobExceptionOccurred('pubsub', $this->job, $e));

        } finally {
            if (! $this->job->isDeleted() && ! $this->job->isReleased() && ! $this->job->hasFailed()) {
                $this->job->release($this->calculateBackoff($this->job));

                event(new JobReleasedAfterException('pubsub', $this->job));
            }

            if ($this->job->hasFailed()) {
                event(new JobFailed('pubsub', $this->job, $e));

                DB::insert('insert into failed_jobs (uuid, connection, queue, payload, exception, failed_at) values (?, ?, ?, ?, ?, ?)', [
                    $this->job->getJobId(),
                    'pubsub',
                    Config::get('queue.connections.pubsub.queue'),
                    $this->job->getRawBody(),
                    $e->getMessage(),
                    now(),
                ]);
            }
        }
    }

    protected function calculateBackoff($job)
    {
        $backoff = explode(
            ',',
            method_exists($job, 'backoff') && ! is_null($job->backoff())
                        ? $job->backoff()
                        : 1
        );

        return (int) ($backoff[$job->attempts() - 1] ?? last($backoff));
    }
}
