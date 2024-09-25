<?php

namespace App\Events\Document;

use App\Contracts\PublishableEvent;
use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Uploaded implements PublishableEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected Document $document,
    ) {}

    public function document(): Document
    {
        return $this->document;
    }

    public function data(): array
    {
        logger('Document uploaded', ['document' => $this->document->load('tags')->toArray()]);

        return $this->document->load(['tags'])->toArray();
    }

    public function topic(): string
    {
        return 'document.uploaded';
    }
}
