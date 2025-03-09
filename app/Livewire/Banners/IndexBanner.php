<?php

namespace App\Livewire\Banners;

use App\Livewire\BaseComponent;
use App\Models\Banner;
use Livewire\WithPagination;

class IndexBanner extends BaseComponent
{
    use WithPagination;

    public function render()
    {
        $items = Banner::query()
            ->with(['item'])
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->orderBy('position')
            ->paginate($this->per_page);

        return view('livewire.banners.index-banner' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function updateFormSort($data)
    {
        foreach ($data as $k => $v) {
            Banner::query()->where('id' , $k)->update(['position' => $v]);
        }
    }

    public function deleteItem($id)
    {
        Banner::destroy($id);
    }
}
