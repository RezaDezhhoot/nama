<?php

namespace App\Livewire\Requests;

use App\Enums\RequestStatus;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexRequest extends BaseComponent
{
    use WithPagination;

    public $status , $item  , $type , $region;

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
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function render()
    {
        $items = Request::query()
            ->with(['plan','user','unit','unit.city','unit.region'])
            ->withCount('comments')
            ->when($this->region , function (Builder $builder) {
                $builder->whereHas('unit' , function (Builder $builder) {
                    $builder->where('region_id' , $this->region);
                });
            })
            ->latest('updated_at')
            ->when($this->type , function ($q) {
                $q->whereHas('item' , function ($q){
                    $q->where('type' , $this->type);
                });
            })
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
