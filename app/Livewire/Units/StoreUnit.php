<?php

namespace App\Livewire\Units;

use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Livewire\BaseComponent;
use App\Models\Unit;

class StoreUnit extends BaseComponent
{
    public $title , $type , $sub_type , $parent , $city , $region , $neighborhood , $area , $auto_accept;
    public $lat , $lng;
    public $regionAjax , $neighborhoodAjax , $areaAjax;

    public function mount($action , $id = null)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->model = Unit::query()->with(['city','parent','region','neighborhood','area'])->findOrFail($id);

            $this->title = $this->model->title;
            $this->header = $this->model->title;
            $this->city = $this->model->city?->toArray() ?? [];
            $this->parent = $this->model->parent_id;
            $this->region = $this->model->region?->toArray() ?? [];
            $this->neighborhood = $this->model->neighborhood?->toArray() ?? [];
            $this->area = $this->model->area?->toArray() ?? [];
            $this->lat = $this->model->lat;
            $this->lng = $this->model->lng;
            $this->auto_accept = $this->model->auto_accept ?? false;
            $this->sub_type = $this->model->sub_type?->value ?? null;
            $this->type = $this->model->type?->value ?? null;
        } elseif ($this->isCreatingMode()) {
            $this->header = 'مرکز جدید';
        } else abort(404);
        $this->data['type'] = UnitType::labels();
        $this->data['sub_type'] = UnitSubType::labels();
        $this->data['parent'] = Unit::query()->when($id , function ($q) use ($id) {
            $q->where('id','!=',$id);
        })->whereNull('parent_id')->pluck('title','id')->toArray();
    }

    public function render()
    {
        return view('livewire.units.store-unit')->extends('livewire.layouts.admin');
    }

    public function updatedCity($v): void
    {
        $this->regionAjax = route('admin.feed.regions',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#region' , $this->regionAjax);
    }

    public function updatedRegion($v): void
    {
        $this->neighborhoodAjax = route('admin.feed.neighborhoods',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#neighborhood' , $this->neighborhoodAjax);
    }

    public function updatedNeighborhood($v): void
    {
        $this->areaAjax = route('admin.feed.areas',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#area' , $this->areaAjax);
    }

    public function deleteItem()
    {
        if ($this->isUpdatingMode()) {
            $this->model->delete();
            redirect()->route('admin.units.index');
        }
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:150'],
            'city' => ['required','exists:cities,id'],
            'region' => ['required','exists:regions,id'],
            'neighborhood' => ['required','exists:neighborhoods,id'],
            'area' => ['nullable'],
            'lat'=> ['required','numeric'],
            'lng'=> ['required','numeric'],
            'type' => ['required','string','max:150'],
            'sub_type' => ['nullable','string','max:150'],
            'parent' => ['nullable','exists:units,id'],
            'auto_accept' => ['nullable','boolean']
        ]);
        $data = [
            'title' => $this->title,
            'city_id' => $this->city,
            'type' => $this->type,
            'sub_type' => emptyToNull($this->sub_type),
            'region_id' => $this->region,
            'neighborhood_id' => $this->neighborhood,
            'area_id' => emptyToNull($this->area),
            'parent_id' => emptyToNull($this->parent),
            'auto_accept' => emptyToNull($this->auto_accept) ?? false,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
        $model = $this->model ?: new Unit();
        $model->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.units.index');
    }
}
