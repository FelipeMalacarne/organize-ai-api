<?php

namespace App\Http\Controllers;

use App\Contracts\DocumentService;
use App\Http\Requests\Document\ListAllRequest;
use App\Http\Requests\Document\UpdateRequest;
use App\Http\Requests\Document\UploadRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListAllRequest $request)
    {
        $documents = $this->service->getAllDocuments($request->user()->id, $request->validated());

        return DocumentResource::collection($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadRequest $request)
    {
        $validated = $request->validated();

        $document = $this->service->uploadDocument([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'metadata' => $validated['metadata'] ?? [],
            'tags' => $validated['tags'],
        ], $request->file('document'));

        return DocumentResource::make($document->load('tags'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $document = $this->service->getDocumentById($id, Auth::user()->id);
        if (! $document) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found',
            ], 404);
        }

        return DocumentResource::make($document);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        if (! $document = $this->service->getDocumentById($id, $request->user()->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found',
            ], 404);
        }

        $document = $this->service->updateDocument($document, $request->validated());

        return DocumentResource::make($document);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = request()->user();

        if (! $document = $this->service->getDocumentById($id, $user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found',
            ], 404);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found',
            ], 404);
        }

        return $this->service->generateDownloadUrl($document);
    }
}
