<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\RequestStep;
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
            'created_at' => $this->created_at,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'images' => MediaResource::collection($this->whenLoaded('images')),
            'video' => MediaResource::make($this->whenLoaded('video')),
            'need_offer_amount' => $this->step === RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,
            'need_final_amount' => $this->step === RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,
            'last_updated_by' => $this->last_updated_by?->title() ?? null
        ];
    }
}
