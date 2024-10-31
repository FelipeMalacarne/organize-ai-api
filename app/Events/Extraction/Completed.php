<?php

namespace App\Events\Extraction;

use App\Models\Document;
use App\Services\DocumentAI\Enums\ProcessorEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Completed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected Document $document,
        protected array $data,
        protected ProcessorEnum $type,
    ) {}

    public function document(): Document
    {
        return $this->document;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function type(): ProcessorEnum
    {
        return $this->type;
    }
}
