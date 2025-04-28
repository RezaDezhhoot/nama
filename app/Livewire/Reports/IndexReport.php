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

    public $status , $item ,$type , $region;

    protected function queryString()
    {
        return [
            'item' => [
                'as' => 'item'
            ]
        ];
    }


    public function mount($type)
    {
        $this->type = $type;
        $this->data['status'] = RequestStatus::labels();
    }

    public function render()
    {
        $items = Report::query()
            ->with(['request','request.user','request.unit','request.unit.city','request.unit.region'])
            ->withCount('comments')
            ->when($this->region , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('unit' , function (Builder $builder) {
                        $builder->where('region_id' , $this->region);
                    });
                });
            })
            ->latest('updated_at')
            ->when($this->type , function ($q) {
                $q->whereHas('item' , function ($q){
                    $q->where('type' , $this->type);
                });
            })
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
