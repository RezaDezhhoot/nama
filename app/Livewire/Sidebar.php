<?php

namespace App\Livewire;

use App\Enums\FormReportEnum;
use App\Enums\RequestStatus;
use App\Enums\UnitType;
use App\Models\FormReport;
use App\Models\Report;
use App\Models\Request;
use App\Models\WrittenRequest;
use Illuminate\Support\Facades\DB;
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
        $university_requests = Request::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::UNIVERSITY);
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

        $university_reports = Report::query()
            ->whereHas('request')
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->whereHas('item' , function ($q){
                $q->where('type' , UnitType::UNIVERSITY);
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

        $reports = FormReport::query()
            ->where('status' , FormReportEnum::PENDING)
            ->count();

        return view('livewire.sidebar' , get_defined_vars());
    }
}
