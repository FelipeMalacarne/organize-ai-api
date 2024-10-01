<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ExtractionResource",
 *     type="object",
 *     title="Extraction",
 *     required={"id", "content"},
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Extraction's unique identifier"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Extracted content from the document"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Extraction creation timestamp"
 *     ),
 * )
 */
class ExtractionResource extends JsonResource
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
            'document' => DocumentResource::make($this->whenLoaded('document')),
            'extracted_text' => $this->extracted_text,
            'created_at' => $this->created_at,
        ];
    }
}
