<?php

namespace App\Services\Pubsub;

use App\Contracts\PublishableEvent;
use Google\Cloud\PubSub\PubSubClient;

class Publisher
{
    public function __construct(
        protected PubSubClient $pubSub,
    ) {}

    public function publish(PublishableEvent $event): void
    {
        $topicName = $event->topic();

        if (! $topic = $this->pubSub->topic($topicName)) {
            $topic = $this->pubSub->createTopic($topicName);
        }

        $topic->publish([
            'data' => json_encode($event->data()),
        ]);
    }
}
