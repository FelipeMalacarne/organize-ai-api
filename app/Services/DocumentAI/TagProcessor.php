<?php

namespace App\Services\DocumentAI;

use App\Services\DocumentAI\Contracts\DocumentProcessor;
use App\Services\DocumentAI\Enums\ProcessorEnum;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\GcsDocument;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Collection;

class TagProcessor implements DocumentProcessor
{
    public function __construct(
        protected DocumentProcessorServiceClient $client,
    ) {}

    public function process(string $filePath): Collection
    {
        $request = new ProcessRequest([
            'name' => ProcessorEnum::Classifier->fullyQualifiedName(),
            'gcs_document' => new GcsDocument([
                'gcs_uri' => 'gs://'.config('filesystems.disks.gcs.bucket')."/$filePath",
                'mime_type' => MimeType::from($filePath),
            ]),
        ]);

        $data = $this->client->processDocument($request)->getDocument();

        $labels = [];
        foreach ($data->getEntities() as $entity) {
            $labelName = $entity->getType();
            $confidence = $entity->getConfidence();

            $labels[] = [
                'label' => $labelName,
                'confidence' => round($confidence, 4),
            ];
        }

        logger()->info('Extracted labels (rótulos):', $labels);

        return collect($labels);
    }
}
