<?php

namespace App\Services\Pubsub;

use App\Contracts\PublishableEvent;
use App\Contracts\PubSubPublisher;
use Google\Cloud\PubSub\PubSubClient;

class Publisher implements PubSubPublisher
{
    public function __construct(
        protected PubSubClient $pubSub,
    ) {}

    public function publish(PublishableEvent $event): void
    {
        $topicName = $event->topic();

        $topic = $this->pubSub->topic($topicName);

        if (! $topic->exists()) {
            $topic = $this->pubSub->createTopic($topicName);
        }

        $topic->publish([
            'data' => json_encode($event->data()),
        ]);
    }
}
