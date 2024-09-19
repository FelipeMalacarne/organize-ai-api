<?php

namespace App\Services\Pubsub;

use Carbon\Carbon;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Support\Facades\DB;
use Kainxspirits\PubSubQueue\Jobs\PubSubJob;
use Throwable;

class JobProcessor
{
    protected PubSubJob $job;

    public function setJob(PubSubJob $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function handle(): void
    {
        event(new JobProcessing($this->job->getConnectionName(), $this->job));

        try {
            $this->job->fire();

            event(new JobProcessed($this->job->getConnectionName(), $this->job));

        } catch (\Exception $e) {
            $this->handleJobException($e);

        }
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
                    config('queue.connections.pubsub.queue'),
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
                        : 0
        );

        return (int) ($backoff[$job->attempts() - 1] ?? last($backoff));
    }
}
