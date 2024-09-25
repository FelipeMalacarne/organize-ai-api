<?php

namespace App\Events\Document;

use App\Contracts\PublishableEvent;

class Uploaded extends DocumentEvent implements PublishableEvent
{
    public function data(): array
    {
        return $this->document->load(['tags'])->toArray();
    }

    public function topic(): string
    {
        return 'document.uploaded';
    }
}
