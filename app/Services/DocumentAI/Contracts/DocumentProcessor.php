<?php

namespace App\Services\DocumentAI\Contracts;

use Illuminate\Support\Collection;

interface DocumentProcessor
{
    public function process(string $filePath): Collection;
}
