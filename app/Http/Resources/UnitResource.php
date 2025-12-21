<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'title' => $this->full,
            'type' => $this->type,
            'sub_type' => $this->sub_type,
            'city' => $this->whenLoaded('city'),
            'region' => $this->whenLoaded('region'),
            'neighborhood' => $this->whenLoaded('neighborhood'),
            'area' => $this->whenLoaded('area'),
            'lat' => $this->lat,
            'lng' => $this->lng,
            'code' => $this->code,
            'parent' => self::make($this->whenLoaded('parent'))
        ];
    }
}
