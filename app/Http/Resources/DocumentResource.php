<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'title' => $this->title,
            'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'metadata' => $this->metadata,
            // 'tags' => TagResource::collection($this->whenLoaded('tags')),
            // 'extractions' => ExtractionResource::collection($this->whenLoaded('extractions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
