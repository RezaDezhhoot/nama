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
            'title' => $this->title,
            'type' => $this->type,
            'sub_type' => $this->brothers,
            'city' => $this->city,
            'region' => $this->region,
            'neighborhood' => $this->neighborhood,
            'area' => $this->area,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'code' => $this->code,
            'parent' => self::make($this->parent)
        ];
    }
}
