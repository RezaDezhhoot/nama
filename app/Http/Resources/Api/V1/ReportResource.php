<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'request_id' => $this->request_id,
            'step' => $this->step,
            'status' => $this->status,
            'students' => $this->students,
            'confirm' => $this->confirm ?? false,
            'body' => $this->whenHas('body'),
            'date' => $this->whenHas('date'),
            'final_amount' => $this->whenHas('final_amount'),
            'message' => $this->message,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'images' => MediaResource::collection($this->whenLoaded('images')),
            'video' => MediaResource::make($this->whenLoaded('video')),
        ];
    }
}
