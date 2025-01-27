<?php

namespace App\Livewire\Dashboards;

use App\Livewire\BaseComponent;

class Dashboard extends BaseComponent
{
    public function render()
    {
        return view('livewire.dashboards.dashboard')->extends('livewire.layouts.admin');
    }
}
