<?php

namespace App\Livewire\DashboardItems;

use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use Livewire\WithPagination;

class IndexItem extends BaseComponent
{

    use WithPagination;

    public function render()
    {
        $items = DashboardItem::query()
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.dashboard-items.index-item' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        DashboardItem::destroy($id);
    }
}
