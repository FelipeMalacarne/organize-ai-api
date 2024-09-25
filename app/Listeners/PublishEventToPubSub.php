<?php

namespace App\Listeners;

use App\Contracts\PublishableEvent;
use App\Contracts\PubSubPublisher;

class PublishEventToPubSub
{
    public function __construct(
        protected PubSubPublisher $publisher,
    ) {}

    public function handle(PublishableEvent $event): void
    {
        $this->publisher->publish($event);
    }
}
