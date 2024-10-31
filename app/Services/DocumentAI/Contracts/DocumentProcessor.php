<?php

namespace App\Services\DocumentAI\Contracts;

interface DocumentProcessor
{
    public function process(string $filePath): array;
}
