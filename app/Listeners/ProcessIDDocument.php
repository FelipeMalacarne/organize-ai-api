<?php

namespace App\Listeners;

use App\Events\Document\Created as DocumentCreated;
use App\Events\Extraction\Completed as ExtractionCompleted;
use App\Services\DocumentAI\Enums\ProcessorEnum;
use App\Services\DocumentAI\IDProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessIDDocument implements ShouldQueue
{
    public function __construct(
        protected IDProcessor $processor,
    ) {}

    public function handle(DocumentCreated $event): void
    {
        logger('Processing document using ID processor, id: '.$event->document()->id);

        $data = $this->processor->process($event->document()->file_path);

        $event->document()->extractions()->create([
            'type' => ProcessorEnum::Extractor->name,
            'extracted_json' => $data->toArray(),
        ]);

        logger('Document processed using ID processor, id: '.$event->document()->id);
    }
}
