<?php

namespace App\Livewire\Requests;

use App\Enums\RequestPlanVersion;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Exports\ExportRequests;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\Region;
use App\Models\Request;
use App\Models\RequestPlan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use League\Uri\UriTemplate\Operator;
use Livewire\WithPagination;

class IndexRequest extends BaseComponent
{
    use WithPagination;

    public $status , $item  , $type , $region;

    public $plan , $unit , $step , $user;

    public $unitModel , $regionModel , $planModel , $version;

    protected function queryString()
    {
        return [
            'item' => [
                'as' => 'item'
            ],
            'status' => [
                'as' => 'status'
            ],
            'type' => [
                'as' => 'type'
            ],
            'region' => [
                'as' => 'region'
            ],
            'plan' => [
                'as' => 'plan'
            ],
            'unit' => [
                'as' => 'unit'
            ],
            'step' => [
                'as' => 'step'
            ],
            'search' => [
                'as' => 'search'
            ],
            'version' => [
                'as' => 'version'
            ]
        ];
    }

    public function mount($type)
    {
        $this->type = $type;
        $this->data['status'] = RequestStatus::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
        $this->data['step'] = RequestStep::labels();
        $this->data['version'] = RequestPlanVersion::values();

        if ($this->unit) {
            $this->unitModel = Unit::query()->find($this->unit)?->toArray();
        }
        if ($this->region) {
            $this->regionModel = Region::query()->find($this->region)?->toArray();
        }
        if ($this->plan) {
            $this->planModel = RequestPlan::query()->find($this->plan)?->toArray();
        }
    }

    public function exportXLSX()
    {
        return (new ExportRequests($this->type,$this->step,$this->plan,$this->unit,$this->region,$this->status,$this->search))->download('requests.xlsx');
    }

    public function render()
    {
        $items = Request::query()
            ->with(['plan','user','unit','unit.city','unit.region','unit.parent','coach'])
            ->when($this->step , function (Builder $builder) {
                $builder->where('step' , $this->step);
            })
            ->when($this->user , function (Builder $builder) {
                $builder->where('user_id' , $this->user);
            })
            ->withCount('comments')
            ->when($this->plan , function (Builder $builder){
                $builder->whereHas('plan' , function (Builder $builder){
                    $builder->where('id',$this->plan);
                });
            })
            ->when($this->version , function (Builder $builder) {
                $builder->whereHas('plan' , function (Builder $builder) {
                    $builder->where('version' , $this->version);
                });
            })
            ->when($this->unit , function (Builder $builder){
                $builder->where('unit_id', $this->unit);
            })
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
                $builder->where(function (Builder $builder) {
                    $builder->search($this->search )->orWhereHas('plan' , function (Builder $builder) {
                        $builder->search($this->search);
                    })->orWhere(function (Builder $builder) {
                        $builder->whereIn('user_id' , User::query()->search($this->search )->take(30)->get()->pluck('id')->toArray());
                    })->orWhereHas('unit' , function (Builder $builder)  {
                        $builder->search($this->search );
                    });
                });
            })->paginate($this->per_page);

        return view('livewire.requests.index-request' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        Request::destroy($id);
    }
}
