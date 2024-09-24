<?php

namespace App\Contracts;

interface PubSubPublisher
{
    public function publish(PublishableEvent $event): void;
}
