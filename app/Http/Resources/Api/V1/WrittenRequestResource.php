<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WrittenRequestResource extends JsonResource
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
            'title' => $this->title,
            'status' => $this->status,
            'step' => $this->step,
            'body' => $this->body,
            'countable' => $this->countable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'letter' => MediaResource::make($this->whenLoaded('letter')),
            'sign' => MediaResource::make($this->whenLoaded('sign')),
            'message' => $this->message,
        ];
    }
}
