<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormItemResource extends JsonResource
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
            'required' => $this->required,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'help' => $this->help,
            'max' => $this->max,
            'min' => $this->min,
            'mime_types' => explode(',' , $this->mime_types),
            'options' => $this->options,
            'conditions' => $this->conditions,
            'sort' => $this->sort,
        ];
    }
}
