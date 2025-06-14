<?php

namespace App\Livewire\Ring;

use App\Livewire\BaseComponent;

class StoreRing extends BaseComponent
{
    public function render()
    {
        return view('livewire.ring.store-ring')->extends('livewire.layouts.admin');
    }
}
