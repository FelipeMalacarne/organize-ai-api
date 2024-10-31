<?php

namespace App\Listeners;

use App\Events\Document\Created as DocumentCreated;
use App\Services\DocumentAI\TagProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;

class TagDocument implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected TagProcessor $processor,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(DocumentCreated $event): void
    {
        logger('Tagging document'.$event->document()->id());

        $event->document()->load(['tags']);

        $data = $this->processor->process($event->document()->file_path);

        $data->filter(fn ($item) => $item['confidence'] > 0.8)
            ->each(fn ($item) => $event->document()->tags()->firstOrCreate([
                'name' => $item['label'],
            ]));

        logger('Document tagged'.$event->document()->id());
    }
}
