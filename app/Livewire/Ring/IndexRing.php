<?php

namespace App\Livewire\Ring;

use App\Enums\UnitType;
use App\Exports\RingExport;
use App\Livewire\BaseComponent;
use App\Models\Ring;
use App\Models\RingMember;
use Livewire\WithPagination;

class IndexRing extends BaseComponent
{
    use WithPagination;

    public $type ;

    protected function queryString()
    {
        return [
            'type' => [
                'as' => 'type'
            ],
            'search' => [
                'as' => 'search'
            ]
        ];
    }

    public function mount()
    {
        $this->data['type'] = UnitType::labels();
    }

    public function deleteItem($id)
    {
        Ring::destroy($id);
    }

    public function deleteMember($id)
    {
        RingMember::destroy($id);
    }

    public function render()
    {
        $items = Ring::query()
            ->latest()
            ->withTrashed()
            ->with(['owner','role','item','members' => function ($q) {
                $q->withTrashed();
            }])
            ->withCount(['members'])
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->when($this->type , function ($q) {
                $q->whereHas('item' , function ($q) {
                    $q->where('type' , $this->type);
                });
            })->paginate($this->per_page);
        return view('livewire.ring.index-ring' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function restoreMember($id)
    {
        $m = RingMember::query()->withTrashed()->find($id);
        $m->restore();
    }

    public function restore($id)
    {
        $r = Ring::query()->withTrashed()->find($id);
        $r->restore();
    }

    public function export()
    {
        return (new RingExport(type: $this->type , q: $this->search))->download('rings.xlsx');
    }
}
