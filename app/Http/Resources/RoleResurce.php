<?php

namespace App\Http\Resources;

use App\Enums\OperatorRole;
use App\Http\Resources\Api\V1\DashboardItemResource;
use App\Http\Resources\Api\V1\UserResource;
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
            'school_coach_type' => $this->school_coach_type?->label(),
            'item_id' => DashboardItemResource::make($this->whenLoaded('item')),
            'ring' => $this->role === OperatorRole::MOSQUE_HEAD_COACH,
            'badge' => $this->role->badge(),
            'created_at' => $this->created_at,
            'causer' => UserResource::make($this->whenLoaded('causer')),
            'editor' => UserResource::make($this->whenLoaded('editor')),
            'unit' => UnitResource::make($this->whenLoaded('unit')),

            'city' => $this->whenLoaded('city'),
            'region' => $this->whenLoaded('region'),
            'neighborhood' => $this->whenLoaded('neighborhood'),
            'area' => $this->whenLoaded('area'),
        ];
    }
}
