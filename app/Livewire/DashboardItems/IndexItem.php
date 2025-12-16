<?php

namespace App\Livewire\DashboardItems;

use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use Livewire\WithPagination;

class IndexItem extends BaseComponent
{

    use WithPagination;

    public function mount()
    {
        $this->authorize('show_dashboard_items');
    }

    public function render()
    {
        $items = DashboardItem::query()
            ->latest('id')
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);

        return view('livewire.dashboard-items.index-item' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        $this->authorize('delete_dashboard_items');
//        DashboardItem::destroy($id);
    }
}
