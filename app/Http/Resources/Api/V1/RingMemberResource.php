<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RingMemberResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'national_code' => $this->national_code,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'phone' => $this->phone,
            'father_name' => $this->father_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => MediaResource::make($this->whenLoaded('image')),
        ];
    }
}
