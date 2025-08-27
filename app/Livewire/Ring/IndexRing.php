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
        $this->authorize('show_rings');
        $this->data['type'] = UnitType::labels();
    }

    public function deleteItem($id)
    {
        $this->authorize('delete_rings');
        Ring::destroy($id);
    }

    public function deleteMember($id)
    {
        $this->authorize('delete_rings');
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
        $this->authorize('delete_rings');
        $m = RingMember::query()->withTrashed()->find($id);
        $m->restore();
    }

    public function restore($id)
    {
        $this->authorize('delete_rings');
        $r = Ring::query()->withTrashed()->find($id);
        $r->restore();
    }

    public function export()
    {
        $this->authorize('export_rings');
        return (new RingExport(type: $this->type , q: $this->search))->download('rings.xlsx');
    }
}
