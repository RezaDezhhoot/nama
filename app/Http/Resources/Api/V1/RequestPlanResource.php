<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestPlanResource extends JsonResource
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
            'status' => $this->status,
            'sub_title' => $this->sub_title,
            'max_number_people_supported' => $this->max_number_people_supported,
            'support_for_each_person_amount' => $this->support_for_each_person_amount,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'max_allocated_request' => $this->max_allocated_request,
            'bold' => $this->bold,
            'body' => $this->body,
            'image' => asset($this->image),
            'previous_requests' => $this->whenCounted('requests'),
            'completed_cycle' => $this->completed_cycle,
            'single_step' => $this->single_step ?? false,
            'golden' => $this->golden ?? false,
            'staff' => $this->staff ?? false,
            'staff_amount' => $this->staff_amount ?? false,
            'requirements' => RequestPlanResource::collection($this->whenLoaded('requirementsv')),
            'active' => sizeof($this->requirementsv->filter(fn ($v) => $v->completed_cycle == 0 )) == 0,

            'imam_letter' => $this->letter_required,
            'area_interface_letter' => $this->letter2_required,
            'images_required' => $this->images_required,

            'show_letter' => $this->show_letter,
            'show_area_interface' => $this->show_area_interface,
            'show_images' => $this->show_images,

            'report_video_required' => $this->report_video_required,
            'report_other_video_required' => $this->report_other_video_required,
            'report_images2_required' => $this->report_images2_required,
            'report_images_required' => $this->report_images_required,

            'show_report_video' => $this->show_report_video,
            'show_report_other_video' => $this->show_report_other_video,
            'show_report_images2' => $this->show_report_images2,
            'show_report_images' => $this->show_report_images,

            'ring_member_required' =>  $this->golden ? ($this->ring_member_required) : false,
            'show_ring_member' => $this->golden ? ($this->show_ring_member) : false,
        ];
    }
}
