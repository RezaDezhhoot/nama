<?php

namespace App\Livewire\Reports;

use App\Enums\RequestStatus;
use App\Livewire\BaseComponent;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexReport extends BaseComponent
{
    use WithPagination;

    public $status ;

    public function mount()
    {
        $this->data['status'] = RequestStatus::labels();
    }

    public function render()
    {
        $items = Report::query()
            ->with(['request','request.user'])
            ->withCount('comments')
            ->latest('updated_at')
            ->whereHas('request' , function (Builder $builder) {
                $builder->when($this->search , function (Builder $builder) {
                    $builder->search($this->search)->orWhereHas('user' , function (Builder $builder) {
                        $builder->search($this->search);
                    });
                });
            })
            ->confirmed()
            ->roleFilter()
            ->when($this->status , function (Builder $builder) {
                $builder->where('status' , $this->status);
            })->paginate($this->per_page);

        return view('livewire.requests.index-report' , get_defined_vars())->extends('livewire.layouts.admin');
    }
}
