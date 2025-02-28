<?php

namespace App\Http\Resources;

use App\Http\Resources\Api\V1\DashboardItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResurce extends JsonResource
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
            'role' => $this->role->label(),
            'role_en' => $this->role,
            'item_id' => DashboardItemResource::make($this->whenLoaded('item')),
        ];
    }
}
