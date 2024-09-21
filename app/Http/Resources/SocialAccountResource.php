<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAccountResource extends JsonResource
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
            'provider_name' => $this->provider_name,
            'provider_id' => $this->provider_id,
            'token' => $this->token,
            'refresh_token' => $this->refresh_token,
            'expires_in' => $this->expires_in,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
