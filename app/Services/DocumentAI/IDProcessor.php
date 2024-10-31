<?php

namespace App\Services\DocumentAI;

use App\Services\DocumentAI\Contracts\DocumentProcessor;
use App\Services\DocumentAI\Enums\ProcessorEnum;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\GcsDocument;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Illuminate\Http\Testing\MimeType;

class IDProcessor implements DocumentProcessor
{
    public function __construct(
        protected DocumentProcessorServiceClient $client,
    ) {}

    public function process(string $filePath): array
    {
        $request = new ProcessRequest([
            'name' => ProcessorEnum::Extractor->fullyQualifiedName(),
            'gcs_document' => new GcsDocument([
                'gcs_uri' => 'gs://'.config('filesystems.disks.gcs.bucket')."/$filePath",
                'mime_type' => MimeType::from($filePath),
            ]),
        ]);

        $data = $this->client->processDocument($request)->getDocument();

        $structuredData = [];
        foreach ($data->getEntities() as $entity) {
            $key = $entity->getType();
            $value = $entity->getMentionText();
            $normalizedValue = $entity->getNormalizedValue()?->getText();
            $structuredData[] = [
                'field' => $key,
                'value' => $value,
                'normalized_value' => $normalizedValue,
            ];
        }

        return $structuredData;
    }
}
