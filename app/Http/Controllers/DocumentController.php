<?php

namespace App\Http\Controllers;

use App\Contracts\DocumentService;
use App\Exceptions\DocumentException;
use App\Http\Requests\Document\ListAllRequest;
use App\Http\Requests\Document\UpdateRequest;
use App\Http\Requests\Document\UploadRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $service,
    ) {}

    public function index(ListAllRequest $request)
    {
        $documents = $this->service->getAllDocuments($request->user()->id, $request->validated());

        return DocumentResource::collection($documents);
    }

    public function store(UploadRequest $request)
    {
        $validated = $request->validated();

        $document = $this->service->uploadDocument([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'metadata' => $validated['metadata'] ?? [],
            'tags' => $validated['tags'] ?? [],
        ], $request->file('document'));

        return DocumentResource::make($document);
    }

    public function show(string $id)
    {
        $document = $this->service->getDocumentById($id, Auth::user()->id);
        if (! $document) {
            throw DocumentException::notFound();
        }

        return DocumentResource::make($document);
    }

    public function update(UpdateRequest $request, string $id)
    {
        if (! $document = $this->service->getDocumentById($id, $request->user()->id)) {
            throw DocumentException::notFound();
        }

        $document = $this->service->updateDocument($document, $request->validated());

        return DocumentResource::make($document);
    }

    public function destroy(string $id)
    {
        $user = request()->user();

        if (! $document = $this->service->getDocumentById($id, $user->id)) {
            throw DocumentException::notFound();
        }

        $this->service->deleteDocument($document, $user);

        return response()->json([
            'status' => 'success',
            'message' => 'Document deleted successfully',
        ]);
    }

    public function download(string $id)
    {
        $document = $this->service->getDocumentById($id, Auth::user()->id);
        if (! $document) {
            throw DocumentException::notFound();
        }

        return $this->service->generateDownloadUrl($document);
    }
}
