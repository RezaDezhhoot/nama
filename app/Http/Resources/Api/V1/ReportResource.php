<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;
        $role = OperatorRole::from(request()->get('role'));

        if ($role !== OperatorRole::MOSQUE_HEAD_COACH) {
            if (! in_array($this->step ,$role->step()) && in_array($this->step ,$role->next())) {
                $status = RequestStatus::DONE;
            } elseif (in_array($role , [OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES,OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING]) && ! in_array($this->step ,$role->step()) && ! in_array($this->step ,$role->next())) {
                $status = null;
            }
        }
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'step' => $this->step,
            'role' => $this->step->role(),
            'status' => $status,
            'students' => $this->students,
            'confirm' => $this->confirm ?? false,
            'body' => $this->whenHas('body'),
            'date' => $this->whenHas('date'),
            'amount' => $this->amount,
            'offer_amount' => $this->offer_amount,
            'final_amount' => $this->final_amount,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'messages' => $this->messages,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'images' => MediaResource::collection($this->whenLoaded('images')),
            'video' => MediaResource::make($this->whenLoaded('video')),
            'need_offer_amount' => $this->step === RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,
            'need_final_amount' => $this->step === RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,
            'last_updated_by' => $this->last_updated_by?->title() ?? null,
            'other_videos' => MediaResource::collection($this->whenLoaded('otherVideos')),
            'images2' => MediaResource::collection($this->whenLoaded('images2')),
        ];
    }
}
