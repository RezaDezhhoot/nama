<?php

namespace App\Livewire\ClientLogs;

use App\Livewire\BaseComponent;
use App\Models\ClientLog;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Log extends BaseComponent
{
    use WithPagination;

    public function render()
    {
        $items = ClientLog::query()
            ->with('user')
            ->latest('created_at')
            ->when($this->search , function (Builder $builder) {
                $builder->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.client-logs.log' , get_defined_vars())->extends('livewire.layouts.admin');
    }
}
