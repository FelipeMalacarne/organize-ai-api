<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DocumentResource",
 *     type="object",
 *     title="Document",
 *     required={"id", "title", "file_type"},
 *
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Unique identifier of the document"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/UserResource",
 *         description="Owner of the document"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the document"
 *     ),
 *     @OA\Property(
 *         property="file_type",
 *         type="string",
 *         description="Type of the file (e.g., pdf, jpg)"
 *     ),
 *     @OA\Property(
 *         property="metadata",
 *         type="object",
 *         description="Additional metadata related to the document"
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         description="Tags associated with the document",
 *
 *         @OA\Items(ref="#/components/schemas/TagResource")
 *     ),
 *
 *     @OA\Property(
 *         property="extractions",
 *         type="array",
 *         description="Extraction data from the document",
 *
 *         @OA\Items(ref="#/components/schemas/ExtractionResource")
 *     ),
 *
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->sqid,
            'resource' => 'document',
            'user' => UserResource::make($this->whenLoaded('user')),
            'title' => $this->title,
            // 'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'metadata' => $this->metadata,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'extractions' => ExtractionResource::collection($this->whenLoaded('extractions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
