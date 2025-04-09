<?php

namespace App\Livewire;

use App\Enums\RequestStatus;
use App\Enums\UnitType;
use App\Models\Report;
use App\Models\Request;
use App\Models\WrittenRequest;
use Livewire\Component;

class Sidebar extends Component
{
    public function render()
    {
        $mosque_requests = Request::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::MOSQUE);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $school_requests = Request::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::SCHOOL);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $center_requests = Request::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::CENTER);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $mosque_reports = Report::query()
            ->whereHas('request')
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::MOSQUE);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $school_reports = Report::query()
            ->whereHas('request')
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::SCHOOL);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $center_reports = Report::query()
            ->whereHas('request')
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::CENTER);
            })
            ->roleFilter()
            ->confirmed()
            ->count();

        $writtenRequests = WrittenRequest::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->roleFilter()
            ->count();

        return view('livewire.sidebar' , get_defined_vars());
    }
}
