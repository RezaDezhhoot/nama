<?php

namespace App\Livewire\Cities;

use App\Enums\PageAction;
use App\Livewire\BaseComponent;
use App\Models\City;
use App\Models\State;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class IndexCity extends BaseComponent
{
    use WithPagination;
    public $title , $region , $state;

    public function mount()
    {
        $this->data['states'] = State::all()->pluck('title','id')->toArray();
        $this->authorize('show_locations');
    }

    public function render()
    {
        $items = City::query()
            ->latest()
            ->withCount(['regions','neighborhoods','state'])
            ->when($this->region , function ($q) {
                $q->whereHas('regions' , function ($q){
                    $q->where('id' , $this->region);
                });
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })->paginate($this->per_page);
        return view('livewire.cities.index-city' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        $this->authorize('delete_locations');
        City::destroy($id);
    }

    public function newCity()
    {
        $this->authorize('create_locations');
        $this->emitShowModal('city');
    }

    public function storeCity()
    {
        $this->authorize('create_locations');
        $this->validate([
            'title' => ['required','string','max:140'],
            'state' => ['nullable' , Rule::exists('states','id')]
        ]);
        $data = [
            'title' => $this->title,
            'state_id' => $this->state
        ];
        $model = City::query()->create($data);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.cities.store',[PageAction::UPDATE,$model->id]);
    }
}
