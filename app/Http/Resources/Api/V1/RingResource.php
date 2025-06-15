<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RingResource extends JsonResource
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
            'name' => $this->name,
            'national_code' => $this->national_code,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'phone' => $this->phone,
            'level_of_education' => $this->level_of_education,
            'field_of_study' => $this->field_of_study,
            'job' => $this->job,
            'sheba_number' => $this->sheba_number,
            'skill_area' => $this->skill_area,
            'functional_area' => $this->functional_area,
            'image' => MediaResource::make($this->whenLoaded('image')),
            'birthdate' => $this->birthdate,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'members' => RingMemberResource::collection($this->whenLoaded('members'))
        ];
    }
}
