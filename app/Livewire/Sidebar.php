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
        $mosque_requests = Request::counter(UnitType::MOSQUE);
        $school_requests = Request::counter(UnitType::SCHOOL);
        $university_requests = 0;
        $center_requests = Request::counter(UnitType::CENTER);
        $garden_requests = Request::counter(UnitType::GARDEN);
        $hall_requests = Request::counter(UnitType::HALL);
        $stadium_requests = Request::counter(UnitType::STADIUM);
        $darul_quran_requests = Request::counter(UnitType::DARUL_QURAN);
        $cultural_institute_requests = Request::counter(UnitType::CULTURAL_INSTITUTE);
        $seminary_requests = Request::counter(UnitType::SEMINARY);
        $quranic_center_requests = Request::counter(UnitType::QURANIC_CENTER);

        $mosque_reports = Report::counter(UnitType::MOSQUE);
        $school_reports = Report::counter(UnitType::SCHOOL);
        $university_reports = 0;
        $center_reports = Report::counter(UnitType::CENTER);
        $garden_reports = Report::counter(UnitType::GARDEN);
        $hall_reports = Report::counter(UnitType::HALL);
        $stadium_reports = Report::counter(UnitType::STADIUM);
        $darul_quran_reports = Report::counter(UnitType::DARUL_QURAN);
        $cultural_institute_reports = Report::counter(UnitType::CULTURAL_INSTITUTE);
        $seminary_reports = Report::counter(UnitType::SEMINARY);
        $quranic_center_reports = Report::counter(UnitType::QURANIC_CENTER);

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
