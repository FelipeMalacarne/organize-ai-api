<?php

namespace App\Events\Document;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class DocumentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected Document $document,
    ) {
        logger('Document event created');
    }

    public function document(): Document
    {
        return $this->document;
    }
}
