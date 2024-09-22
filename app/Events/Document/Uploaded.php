<?php

namespace App\Events\Document;

use App\Models\Document;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Uploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected Document $document,
    ) {}

    public function document(): Document
    {
        return $this->document;
    }
}
