<?php

namespace App\Livewire\CampTickets;

use App\Enums\RequestStatus;
use App\Livewire\BaseComponent;
use App\Models\CampTicket;
use Illuminate\Validation\Rule;

class Ticket extends BaseComponent
{
    public $request;

    public function mount($action)
    {
        $this->setMode($action);
        if ($this->isCreatingMode()) {
            $this->header = 'بلیط';
        } else abort(404);
    }

    public function store()
    {
        $this->validate([
            'request' => ['required',Rule::exists('requests','id')->where('status',RequestStatus::DONE->value)]
        ]);
        $data = [
            'request_id' => $this->request
        ];
        CampTicket::query()->create($data);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.camp-tickets.index');
    }

    public function render()
    {
        return view('livewire.camp-tickets.ticket')->extends('livewire.layouts.admin');
    }
}
