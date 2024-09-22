<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use App\Models\Document;
use Illuminate\Support\Collection;

interface DocumentService
{
    public function uploadDocument(array $data, UploadedFile $file): Document;

    public function getAllDocuments(int $userId, array $filters = []): Collection;

    public function getDocumentById(int $id, int $userId): ?Document;

    public function updateDocument(Document $document, array $data): Document;

    public function deleteDocument(Document $document): bool;

    public function generateDownloadUrl(Document $document): string;
}
