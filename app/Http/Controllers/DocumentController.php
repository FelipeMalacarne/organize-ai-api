<?php

namespace App\Http\Controllers;

use App\Contracts\DocumentService;
use App\Exceptions\DocumentException;
use App\Http\Requests\Document\ListAllRequest;
use App\Http\Requests\Document\UpdateRequest;
use App\Http\Requests\Document\UploadRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Documents",
 *     description="API Endpoints for Document Management"
 * )
 */
class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $service,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/documents",
     *     summary="List all documents",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of documents",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DocumentResource")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function index(ListAllRequest $request)
    {
        $documents = $this->service->getAllDocuments($request->user()->id, $request->validated());

        return DocumentResource::collection($documents);
    }

    /**
     * @OA\Post(
     *     path="/api/documents",
     *     summary="Upload a new document",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Document upload payload",
     *
     *         @OA\JsonContent(
     *             required={"document"},
     *
     *             @OA\Property(
     *                 property="document",
     *                 type="string",
     *                 format="binary",
     *                 description="The document file to upload"
     *             ),
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the document"
     *             ),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 description="Tags associated with the document",
     *
     *                 @OA\Items(type="string")
     *             ),
     *
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 description="Additional metadata for the document"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Document uploaded successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/DocumentResource")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
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

        return DocumentResource::make($document);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/{id}",
     *     summary="Get a specific document",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Document ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of the document",
     *
     *         @OA\JsonContent(ref="#/components/schemas/DocumentResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Document not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function show(string $id)
    {
        $document = $this->service->getDocumentById($id, Auth::user()->id);
        if (! $document) {
            throw DocumentException::notFound();
        }

        return DocumentResource::make($document);
    }

    /**
     * @OA\Put(
     *     path="/api/documents/{id}",
     *     summary="Update a specific document",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Document ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Document update payload",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the document"
     *             ),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 description="Tags associated with the document",
     *
     *                 @OA\Items(type="string")
     *             ),
     *
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 description="Additional metadata for the document"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Document updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/DocumentResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Document not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function update(UpdateRequest $request, string $id)
    {
        if (! $document = $this->service->getDocumentById($id, $request->user()->id)) {
            throw DocumentException::notFound();
        }

        $document = $this->service->updateDocument($document, $request->validated());

        return DocumentResource::make($document);
    }

    /**
     * @OA\Delete(
     *     path="/api/documents/{id}",
     *     summary="Delete a specific document",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Document ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Document deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Document deleted successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Document not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/documents/{id}/download",
     *     summary="Download a specific document",
     *     tags={"Documents"},
     *     security={{"BearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Document ID",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Download URL generated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="download_url",
     *                 type="string",
     *                 description="URL to download the document"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Document not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function download(string $id)
    {
        $document = $this->service->getDocumentById($id, Auth::user()->id);
        if (! $document) {
            throw DocumentException::notFound();
        }

        return $this->service->generateDownloadUrl($document);
    }
}
