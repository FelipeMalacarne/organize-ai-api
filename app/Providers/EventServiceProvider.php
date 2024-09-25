<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider; // <- there

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        \App\Events\Document\Uploaded::class => [
            \App\Listeners\PublishEventToPubSub::class,
        ],

    ];
}
