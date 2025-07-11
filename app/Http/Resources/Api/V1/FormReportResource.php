<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\FormItemType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormReportResource extends JsonResource
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
            'status' => $this->status,
            'message' => $this->message,
            'reports' => collect($this->reports)->map(function ($v){
                if ($v['form']['type'] == FormItemType::FILE->value){
                    $v['value'] = asset($v['value']);
                }
                return $v;
            }),
            'form' => FormResource::make($this->whenLoaded('form')),
            'created_at' => $this->created_at,
        ];
    }
}
