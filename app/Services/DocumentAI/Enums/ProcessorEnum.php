<?php

namespace App\Services\DocumentAI\Enums;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;

enum ProcessorEnum: string
{
    case Classifier = 'c4b723ed153b647f';
    case Extractor = '9cc1da015e77e6a3';

    public function fullyQualifiedName(): string
    {
        return DocumentProcessorServiceClient::processorName(config('services.gcp.project_id'), 'us', $this->value);
    }

    public static function fromName(string $name): ProcessorEnum
    {
        return match ($name) {
            'Classifier' => self::Classifier,
            'Extractor' => self::Extractor,
            'OCR' => self::Extractor,
        };
    }

    public function outputFormat(): string
    {
        return match ($this) {
            self::Classifier => 'json',
            self::Extractor => 'json',
        };
    }
}
