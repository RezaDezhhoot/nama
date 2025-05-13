<?php

namespace App\Livewire\Reports;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Exports\ExportReports;
use App\Livewire\BaseComponent;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexReport extends BaseComponent
{
    use WithPagination;

    public $status , $item ,$type , $region;
    public $plan , $unit , $step;

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
        $this->data['step'] = RequestStep::labels();
    }

    public function exportXLSX()
    {
        return (new ExportReports($this->type,$this->step,$this->plan,$this->unit,$this->region,$this->status,$this->search))->download('reports.xlsx');
    }

    public function render()
    {
        $items = Report::query()
            ->with(['request','request.user','request.unit','request.unit.city','request.unit.region','request.plan'])
            ->when($this->step , function (Builder $builder) {
                $builder->where('step' , $this->step);
            })
            ->withCount('comments')
            ->when($this->region , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('unit' , function (Builder $builder) {
                        $builder->where('region_id' , $this->region);
                    });
                });
            })
            ->when($this->unit , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('unit' , function (Builder $builder) {
                        $builder->where('id' , $this->unit);
                    });
                });
            })
            ->when($this->plan , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('plan' , function (Builder $builder){
                        $builder->where('id',$this->plan);
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
                    $builder->search($this->search )->orWhereHas('plan' , function (Builder $builder) {
                        $builder->search($this->search);
                    })->orWhere(function (Builder $builder) {
                        $builder->whereIn('user_id' , User::query()->search($this->search )->take(30)->get()->pluck('id')->toArray());
                    })->orWhereHas('unit' , function (Builder $builder)  {
                        $builder->search($this->search );
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
