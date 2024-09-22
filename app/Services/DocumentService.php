<?php

namespace App\Services;

use App\Contracts\DocumentService as DocumentServiceContract;
use App\Events\Document\Uploaded as DocumentUploaded;
use App\Models\Document;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService implements DocumentServiceContract
{
    public function uploadDocument(array $data, UploadedFile $file): Document
    {
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        Storage::disk('gcs')->putFileAs('documents', $file, $filename);

        $document = Document::create([
            'user_id' => Arr::get($data, 'user_id'),
            'title' => Arr::get($data, 'title'),
            'file_path' => 'documents/'.$filename,
            'file_type' => $file->getClientOriginalExtension(),
            'metadata' => Arr::get($data, 'metadata'),
        ]);

        if ($tags = Arr::get($data, 'tags')) {
            $tagIds = collect($tags)
                ->map(fn ($tag) => Tag::forUser(Arr::get($data, 'user_id'))->firstOrCreate(['name' => $tag])->id);
            $document->tags()->sync($tagIds);
        }

        DocumentUploaded::dispatch($document);

        return $document;
    }

    public function getAlldocuments(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Document::where('user_id', $userId)
            ->with(['tags', 'extractions']);

        if ($title = Arr::get($filters, 'title')) {
            $query->where('title', 'like', '%'.$title.'%');
        }

        return $query->paginate(Arr::get($filters, 'limit', 20), ['*'], 'page', Arr::get($filters, 'page', 1));
    }

    public function getDocumentById(int $id, int $userId): ?Document
    {
        return Document::where('id', $id)
            ->where('user_id', $userId)
            ->with(['tags', 'extractions'])
            ->first();
    }

    public function updateDocument(Document $document, array $data): Document
    {
        $document->update([
            'title' => Arr::get($data, 'title', $document->title),
            'metadata' => Arr::get($data, 'metadata', $document->metadata),
        ]);

        if ($tags = Arr::get($data, 'tags')) {
            $tagIds = collect($tags)
                ->map(fn ($tag) => Tag::forUser($document->user_id)->firstOrCreate(['name' => $tag])->id);

            $document->tags()->sync($tagIds);
        }

        return $document;
    }

    public function deleteDocument(Document $document): bool
    {
        return $document->delete();
    }

    public function generateDownloadUrl(Document $document): string
    {
        return Storage::disk('gcs')->temporaryUrl($document->file_path, now()->addMinutes(15));
    }
}
