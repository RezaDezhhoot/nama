<?php

namespace App\Livewire\Plans;

use App\Enums\PlanTypes;
use App\Enums\RequestPlanStatus;
use App\Livewire\BaseComponent;
use App\Models\RequestPlan;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexPlan extends BaseComponent
{
    use WithPagination;

    public $status;

    public $single_step, $staff ,$golden , $type , $designated_by_council;

    public function mount()
    {
        $this->authorize('show_request_plans');
        $this->data['status'] = RequestPlanStatus::labels();
        $this->data['types'] = PlanTypes::labels();
    }

    public function render()
    {
        $items = RequestPlan::query()
            ->with(['requirements'])
            ->latest()
            ->when($this->single_step , function (Builder $builder) {
                $builder->where('single_step' , true);
            })
            ->when($this->staff , function (Builder $builder) {
                $builder->where('staff' , true);
            })
            ->when($this->designated_by_council , function (Builder $builder) {
                $builder->where('designated_by_council' , true);
            })
            ->when($this->type , function (Builder $builder) {
                $builder->where('type' , $this->type);
            })
            ->when($this->golden , function (Builder $builder) {
                $builder->where('golden' , true);
            })
            ->when($this->status , function ($q) {
                $q->where('status' , $this->status);
            })->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.plans.index-plan' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        $this->authorize('delete_request_plans');
        RequestPlan::destroy($id);
    }
}
