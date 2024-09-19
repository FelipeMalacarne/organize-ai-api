<?php

namespace App\Services\Pubsub;

use Google\Cloud\PubSub\Message;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Kainxspirits\PubSubQueue\Jobs\PubSubJob;

class PubSubEvent
{
    protected Message $message;

    public function __construct(
        protected Container $container
    ) {}

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function createJob(): PubSubJob
    {
        return new PubSubJob(
            $this->container,
            Queue::connection('pubsub'),
            $this->message,
            Config::get('queue.connections.pubsub.driver'),
            Config::get('queue.connections.pubsub.queue'),
        );
    }

    // TODO: implement the logic later of dispatching laravel events using messages from GCP pubsub
    public function dispatchEvent(): void
    {
        $eventClass = $this->message->data()['event'];

        $attributes = $this->message->attributes();

        event( new $eventClass($attributes) );
    }
}
