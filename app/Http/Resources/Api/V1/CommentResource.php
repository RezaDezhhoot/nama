<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->commentable instanceof Report) {
            $commentable = $this->commentable->request;
        } else {
            $commentable =  $this->commentable;
        }
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'display_name' => $this?->step?->title($commentable?->item?->type),
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'step' => $this->step,
            'user' => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
