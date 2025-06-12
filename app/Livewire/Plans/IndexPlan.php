<?php

namespace App\Livewire\Plans;

use App\Enums\RequestPlanStatus;
use App\Livewire\BaseComponent;
use App\Models\RequestPlan;
use Livewire\WithPagination;

class IndexPlan extends BaseComponent
{
    use WithPagination;

    public $status;

    public function mount()
    {
        $this->data['status'] = RequestPlanStatus::labels();
    }

    public function render()
    {
        $items = RequestPlan::query()
            ->with(['requirements'])
            ->latest()
            ->when($this->status , function ($q) {
                $q->where('status' , $this->status);
            })->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.plans.index-plan' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        RequestPlan::destroy($id);
    }
}
