<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Http\Resources\UnitResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'request_plan' => RequestPlanResource::make($this->whenLoaded('plan')),
            'step' => $this->step,
            'role' => $this->step->role(),
            'status' => $status,
            'students' => $this->whenHas('students'),
            'amount' => $this->whenHas('amount'),
            'total_amount' => $this->whenHas('total_amount'),
            'final_amount' => $this->whenHas('final_amount'),
            'offer_amount' => $this->whenHas('offer_amount'),
            'date' => $this->whenHas('date'),
            'confirm' => $this->confirm ?? false,
            'body' => $this->whenHas('body'),
            'imam_letter' => MediaResource::make($this->whenLoaded('imamLetter')),
            'area_interface_letter' => MediaResource::make($this->whenLoaded('areaInterfaceLetter')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'messages' => $this->messages,
            'message' => $this->whenHas('message'),
            'report' => ReportResource::make($this->whenLoaded('report')),
            'need_offer_amount' => $this->step === RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,
            'need_final_amount' => $this->step === RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,
            'last_updated_by' => $this->last_updated_by?->title() ?? null,
            'unit' => UnitResource::make($this->whenLoaded('unit')),
            'item' => DashboardItemResource::make($this->whenLoaded('item')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
