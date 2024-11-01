<?php

namespace App\Listeners;

use App\Events\Document\Created as DocumentCreated;
use App\Events\Extraction\Completed as ExtractionCompleted;
use App\Services\DocumentAI\Enums\ProcessorEnum;
use App\Services\DocumentAI\TagProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;

class TagDocument implements ShouldQueue
{
    public function __construct(
        protected TagProcessor $processor,
    ) {}

    public function handle(DocumentCreated $event): void
    {
        logger('Tagging document, id: '.$event->document()->id);

        $event->document()->load(['tags']);

        $data = $this->processor->process($event->document()->file_path);

        $event->document()->extractions()->create([
            'type' => ProcessorEnum::Classifier->name,
            'extracted_json' => $data->toArray(),
        ]);

        $data->filter(fn ($item) => $item['confidence'] > 0.8)
            ->each(fn ($item) => $event->document()->tags()->firstOrCreate([
                'name' => $item['label'],
            ]));

        logger('Document tagged, id: '.$event->document()->id);
    }
}
