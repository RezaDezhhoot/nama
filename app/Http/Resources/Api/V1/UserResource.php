<?php

namespace App\Http\Resources\Api\V1;

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
            'nama_role' => $this->nama_role,
            'arman_role' => $this->role,
            'avatar' => $this->avatar ? "https://armaniran.org/".trim($this->avatar,'/') : null,
        ];
    }
}
