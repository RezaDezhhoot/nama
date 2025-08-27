<?php

namespace App\Livewire\Forms;

use App\Enums\FormStatus;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\Form;
use Livewire\WithPagination;

class IndexForm extends BaseComponent
{
    use WithPagination;

    public $item , $status;

    protected function queryString()
    {
        return [
            'item' => [
                'as' => 'item'
            ],
            'status' => [
                'as' => 'status'
            ],
            'search' => [
                'as' => 'search'
            ]
        ];
    }

    public function mount()
    {
        $this->authorize('show_forms');
        $this->data['status'] = FormStatus::labels();
        $this->data['items'] = DashboardItem::all()->pluck('title','id');
    }

    public function render()
    {
        $items = Form::query()
            ->latest()
            ->with(['item'])
            ->orderBy('sort')
            ->when($this->status , function ($q) {
                $q->where('status',  $this->status);
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->when($this->item , function ($q) {
                $q->where('item_id' , $this->item);
            })->paginate($this->per_page);

        return view('livewire.forms.index-form' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        $this->authorize('delete_forms');
        Form::destroy($id);
    }
}
