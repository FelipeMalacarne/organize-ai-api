<?php

namespace App\Listeners;

use App\Events\Extraction\Completed as ExtractionCompleted;
use App\Models\Extraction;

class CreateNewExtraction
{
    public function handle(ExtractionCompleted $event): void
    {
        logger('Creating new extraction', [
            'document' => $event->document()->id,
            'type' => $event->type()->name,
        ]);

        $extraction = new Extraction([
            'type' => $event->type()->name,
        ]);

        $extraction->extracted_json = $event->data();

        $event->document()->extractions()->save($extraction);

        logger('New extraction created', [
            'document' => $event->document()->id,
            'extraction' => $extraction,
        ]);
    }
}
