<?php

namespace App\Livewire\Units;

use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Livewire\BaseComponent;
use App\Models\Unit;

class StoreUnit extends BaseComponent
{
    public $qtype , $qregion , $qunit , $qsearch;

    public function queryString()
    {
        return [
            'qtype' => [
                'as' => 'type'
            ],
            'qregion' => [
                'as' => 'region'
            ],
            'qunit' => [
                'as' => 'unit'
            ],
            'qsearch' => [
                'as' => 'search'
            ]
        ];
    }

    public $title , $type , $sub_type , $parent , $city , $region , $neighborhood , $area , $auto_accept;
    public $lat , $lng;
    public $regionAjax , $neighborhoodAjax , $areaAjax;
    public $code;

    public $phone1 , $phone1_title;
    public $phone2 , $phone2_title;
    public $phone3 , $phone3_title;
    public $phone4 , $phone4_title;
    public $phone5 , $phone5_title;
    public $phone6 , $phone6_title;
    public $phone7 , $phone7_title;
    public $phone8 , $phone8_title;

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

            $this->phone1 = $this->model->phone1;
            $this->phone1_title = $this->model->phone1_title;
            $this->phone2 = $this->model->phone2;
            $this->phone2_title = $this->model->phone2_title;
            $this->phone3 = $this->model->phone3;
            $this->phone3_title = $this->model->phone3_title;
            $this->phone4 = $this->model->phone4;
            $this->phone4_title = $this->model->phone4_title;
            $this->phone5 = $this->model->phone5;
            $this->phone5_title = $this->model->phone5_title;
            $this->phone6 = $this->model->phone6;
            $this->phone6_title = $this->model->phone6_title;
            $this->phone7 = $this->model->phone7;
            $this->phone7_title = $this->model->phone7_title;
            $this->phone8 = $this->model->phone8;
            $this->phone8_title = $this->model->phone8_title;
            $this->code = $this->model->code;
            $this->updatedCity($this->model->city_id);
            $this->updatedRegion($this->model->region_id);
            $this->updatedNeighborhood($this->model->neighborhood_id);
        } elseif ($this->isCreatingMode()) {
            $this->header = 'مرکز جدید';
        } else abort(404);
        $this->data['type'] = UnitType::labels();
        $this->data['parent'] = Unit::query()->when($id , function ($q) use ($id) {
            $q->where('id','!=',$id);
        })->whereNull('parent_id')->pluck('title','id')->toArray();
        $this->data['sub_type'] = [];
        $this->updatedType($this->type);
    }

    public function updatedType($value)
    {
        $this->data['sub_type'] = [];
        if ($value == UnitType::SCHOOL->value) {
            $this->data['sub_type'] = [
                UnitSubType::MALE->value => UnitSubType::MALE->label(),
                UnitSubType::FEMALE->value => UnitSubType::FEMALE->label(),
                UnitSubType::SUPPORT->value => UnitSubType::SUPPORT->label(),
            ];
        } elseif ($value == UnitType::MOSQUE->value) {
            $this->data['sub_type'] = [
                UnitSubType::BROTHERS->value => UnitSubType::BROTHERS->label(),
                UnitSubType::SISTERS->value => UnitSubType::SISTERS->label(),
            ];
        }
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
            'parent' => [$this->type == UnitType::MOSQUE->value ? 'nullable' : 'required','exists:units,id'],
            'auto_accept' => ['nullable','boolean'],
            'phone1' => ['nullable','string','max:100'],
            'phone1_title' => ['nullable','string','max:100'],
            'phone2' => ['nullable','string','max:100'],
            'phone2_title' => ['nullable','string','max:100'],
            'phone3' => ['nullable','string','max:100'],
            'phone3_title' => ['nullable','string','max:100'],
            'phone4' => ['nullable','string','max:100'],
            'phone4_title' => ['nullable','string','max:100'],
            'phone5' => ['nullable','string','max:100'],
            'phone5_title' => ['nullable','string','max:100'],
            'phone6' => ['nullable','string','max:100'],
            'phone6_title' => ['nullable','string','max:100'],
            'phone7' => ['nullable','string','max:100'],
            'phone7_title' => ['nullable','string','max:100'],
            'phone8' => ['nullable','string','max:100'],
            'phone8_title' => ['nullable','string','max:100'],
            'code' => ['nullable','string','max:150']
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
            'code' => $this->code,
            'phone1' => $this->phone1,
            'phone1_title' => $this->phone1,
            'phone2' => $this->phone2,
            'phone2_title' => $this->phone2_title,
            'phone3' => $this->phone3,
            'phone3_title' => $this->phone3_title,
            'phone4' => $this->phone4,
            'phone4_title' => $this->phone4_title,
            'phone5' => $this->phone5,
            'phone5_title' => $this->phone5_title,
            'phone6' => $this->phone6,
            'phone6_title' => $this->phone6_title,
            'phone7' => $this->phone7,
            'phone7_title' => $this->phone7_title,
            'phone8' => $this->phone8,
            'phone8_title' => $this->phone8_title,
        ];
        $model = $this->model ?: new Unit();
        $model->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.units.index' , [
            'type' => $this->qtype,
            'region' => $this->qregion,
            'unit' => $this->qunit,
            'search' => $this->qsearch,
        ]);
    }
}
