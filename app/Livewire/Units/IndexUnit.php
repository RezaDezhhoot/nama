<?php

namespace App\Livewire\Units;

use App\Enums\UnitType;
use App\Exports\ExportUnits;
use App\Livewire\BaseComponent;
use App\Models\Unit;
use Livewire\WithPagination;

class IndexUnit extends BaseComponent
{
    use WithPagination;

    public $type;

    public function mount()
    {
        $this->data['type'] = UnitType::labels();

    }

    public function render()
    {
        $items = Unit::query()
            ->latest()
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
