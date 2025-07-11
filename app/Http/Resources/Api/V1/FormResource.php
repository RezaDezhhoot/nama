<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
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
            'title'=> $this->title,
            'required' => $this->required,
            'item_count' => $this->whenCounted('items'),
            'items' => FormItemResource::collection($this->whenLoaded('items')),
            'report' => FormReportResource::make($this->whenLoaded('report'))
        ];
    }
}
