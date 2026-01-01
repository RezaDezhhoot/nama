<?php

namespace App\Livewire\CampTickets;

use App\Enums\UnitType;
use App\Livewire\BaseComponent;
use App\Models\CampTicket;
use Livewire\Component;
use Livewire\WithPagination;

class Tickets extends BaseComponent
{
    use WithPagination;

    public $item;

    public function mount()
    {
        $this->data['type'] = UnitType::labels();
    }

    public function render()
    {
        $items = CampTicket::query()
            ->latest()
            ->with(['request','request.item'])
            ->when($this->item , function ($q) {
                $q->whereHas('request' , function ($q){
                    $q->whereHas('item' , function ($q) {
                        $q->where('type' , $this->item);
                    });
                });
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.camp-tickets.tickets' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        CampTicket::destroy($id);
    }
}
