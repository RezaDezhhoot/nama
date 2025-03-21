<?php

namespace App\Livewire;

use App\Enums\RequestStatus;
use App\Models\Report;
use App\Models\Request;
use App\Models\WrittenRequest;
use Livewire\Component;

class Sidebar extends Component
{
    public function render()
    {
        $requests = Request::query()
            ->where('status' , RequestStatus::IN_PROGRESS)
            ->roleFilter()
            ->confirmed()
            ->count();

        $reports = Report::query()
            ->whereHas('request')
            ->where('status' , RequestStatus::IN_PROGRESS)
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
