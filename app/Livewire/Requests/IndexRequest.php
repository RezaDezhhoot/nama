<?php

namespace App\Livewire\Requests;

use App\Enums\RequestStatus;
use App\Livewire\BaseComponent;
use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexRequest extends BaseComponent
{
    use WithPagination;

    public $status ;


    public function mount()
    {
        $this->data['status'] = RequestStatus::labels();
    }

    public function render()
    {
        $items = Request::query()
            ->with(['plan','user','unit'])
            ->withCount('comments')
            ->latest('updated_at')
            ->whereHas('plan')
            ->confirmed()
            ->roleFilter()
            ->when($this->status , function (Builder $builder) {
                $builder->where('status' , $this->status);
            })->when($this->search , function (Builder $builder) {
                $builder->search($this->search)->orWhereHas('user' , function (Builder $builder) {
                    $builder->search($this->search);
                });
            })->paginate($this->per_page);

        return view('livewire.requests.index-request' , get_defined_vars())->extends('livewire.layouts.admin');
    }
}
