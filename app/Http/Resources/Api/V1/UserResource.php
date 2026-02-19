<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\RoleResurce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'national_id' => $this->national_id,
            'nama_role' => $this->nama_role,
            'arman_role' => $this->role,
            'avatar' => $this->avatar ? (filter_var($this->avatar , FILTER_VALIDATE_URL) ? $this->avatar : "https://terminal.app/public/".ltrim($this->avatar,'/')) : null,
            'roles' => RoleResurce::collection($this->whenLoaded('roles2'))
        ];
    }
}
