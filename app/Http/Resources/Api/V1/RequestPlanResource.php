<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestPlanResource extends JsonResource
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
            'sub_title' => $this->sub_title,
            'max_number_people_supported' => $this->max_number_people_supported,
            'support_for_each_person_amount' => $this->support_for_each_person_amount,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'max_allocated_request' => $this->max_allocated_request,
            'bold' => $this->bold,
            'body' => $this->body,
            'image' => asset($this->image),
            'previous_requests' => $this->whenCounted('requests'),
            'completed_cycle' => $this->completed_cycle,
            'imam_letter' => $this->letter_required,
            'area_interface_letter' => $this->letter2_required,
            'single_step' => $this->single_step ?? false,
            'requirements' => RequestPlanResource::collection($this->whenLoaded('requirementsv')),
            'active' => sizeof($this->requirementsv->filter(fn ($v) => $v->completed_cycle == 0 )) == 0
        ];
    }
}
