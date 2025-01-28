<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'thumbnail' => $this?->whenAppended('thumbnail_url'),
            'original' => $this?->whenAppended('url'),
            'disk' => $this->disk ?? null,
            'mime_type' => $this->mime_type ?? null,
            'size' => $this->size ?? null,
            'duration' => $this->duration ?? null,
        ];
    }
}
