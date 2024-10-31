<?php

namespace App\Listeners;

use App\Services\DocumentAI\IDProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\Extraction\Completed as ExtractionCompleted;
use App\Services\DocumentAI\Enums\ProcessorEnum;

class ProcessIDDocument implements ShouldQueue
{
    public function __construct(
        protected IDProcessor $processor,
    ) {}

    public function handle(object $event): void
    {
        logger('Processing document using ID processor, id: '.$event->document()->id);

        $data = $this->processor->process($event->document()->file_path);

        dispatch(new ExtractionCompleted(
            $event->document(),
            $data->toArray(),
            ProcessorEnum::Extractor
        ));

        logger('Document processed using ID processor, id: '.$event->document()->id);
    }
}
