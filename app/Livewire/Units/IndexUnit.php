<?php

namespace App\Livewire\Units;

use App\Enums\UnitType;
use App\Exports\ExportUnits;
use App\Livewire\BaseComponent;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class IndexUnit extends BaseComponent
{
    use WithPagination;

    public $type , $region , $unit;

    public function queryString()
    {
        return [
            'type' => [
                'as' => 'type'
            ],
            'region' => [
                'as' => 'region'
            ],
            'unit' => [
                'as' => 'unit'
            ]
        ];
    }

    public function mount()
    {
        $this->data['type'] = UnitType::labels();

    }

    public function render()
    {
        $items = Unit::query()
            ->latest()
            ->with(['roles','roles.user'])
            ->when($this->region , function (Builder $builder) {
                $builder->where('region_id' , $this->region);
            })->when($this->unit , function (Builder $builder) {
                $builder->where('parent_id' , $this->unit);
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->when($this->type , function ($q) {
                $q->where('type' , $this->type);
            })->paginate($this->per_page);
        return view('livewire.units.index-unit' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function export()
    {
        return (new ExportUnits($this->type))->download('units.xlsx');
    }

    public function deleteItem($id)
    {
        Unit::destroy($id);
    }
}
