<?php

namespace App\Listeners;

use App\Events\Document\Created as DocumentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class TagDocument implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DocumentCreated $event): void
    {
        logger('Tagging document');
        logger($event->document());
    }
}
