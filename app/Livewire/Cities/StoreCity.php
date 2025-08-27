<?php

namespace App\Livewire\Cities;

use App\Imports\Area2Import;
use App\Imports\AreaImport;
use App\Livewire\BaseComponent;
use App\Models\Area;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Region;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class StoreCity extends BaseComponent
{
    public $title;

    public $region , $rTitle , $neighborhoods;

    public $nTitle , $neighborhood , $areas;

    public $aTitle , $area;

    use WithFileUploads;

    public function mount($action , $id = null)
    {
        $this->authorize('edit_locations');
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->model = City::query()->findOrFail($id);
            $this->title = $this->model->title;
            $this->header = $this->title;
        } else abort(404);
    }

    public function storeRegion()
    {
        $this->validate([
            'rTitle' => ['required','string','max:150'],
            'neighborhoods' => ['nullable',Rule::file()->extensions('xlsx')]
        ]);
        $model = $this->region ?: new Region;
        $model->city()->associate($this->model);
        $model->fill([
            'title'  => $this->rTitle
        ])->save();
        if ($this->neighborhoods) {
            Excel::import(new AreaImport($model),$this->neighborhoods);
        }
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        $this->emitHideModal('region');
        $this->reset(['rTitle','region','neighborhoods']);
    }

    public function storeNeighborhood()
    {
        $this->validate([
            'nTitle' => ['required','string','max:150'],
            'areas' => ['nullable',Rule::file()->extensions('xlsx')]
        ]);
        $model = $this->neighborhood ?: new Neighborhood
        ;
        $model->fill([
            'title'  => $this->nTitle
        ])->save();
        if ($this->areas) {
            Excel::import(new Area2Import($model),$this->areas);
        }
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        $this->emitHideModal('neighborhood');
        $this->reset(['nTitle','neighborhood','areas']);
    }

    public function deleteItem()
    {
        $this->authorize('delete_locations');
        $this->model->delete();
        redirect()->route('admin.cities.index');
    }

    public function storeArea()
    {
        $this->validate([
            'aTitle' => ['required','string','max:150'],
        ]);
        $model = $this->area ?: new Area;
        $model->fill([
            'title'  => $this->aTitle
        ])->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        $this->emitHideModal('area');
        $this->reset(['aTitle','area']);
    }

    public function store()
    {
        $this->validate([
            'title' => ['required','string','max:150']
        ]);
        $data = [
            'title' => $this->title
        ];
        $this->model->fill($data)->save();
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        redirect()->route('admin.cities.index');
    }

    public function deleteRegion($id)
    {
        $this->authorize('delete_locations');
        Region::destroy($id);
    }

    public function deleteNeighborhood($id)
    {
        $this->authorize('delete_locations');
        Neighborhood::destroy($id);
    }

    public function deleteArea($id)
    {
        $this->authorize('delete_locations');
        Area::destroy($id);
    }

    public function regionForm($id = null)
    {
        if ($id) {
            $this->region = Region::query()->findOrFail($id);
            $this->rTitle = $this->region->title;
        }
        $this->emitShowModal('region');
    }

    public function neighborhoodForm($id)
    {
        $this->neighborhood = Neighborhood::query()->findOrFail($id);
        $this->nTitle = $this->neighborhood->title;
        $this->emitShowModal('neighborhood');
    }

    public function areaForm($id)
    {
        $this->area = Area::query()->findOrFail($id);
        $this->aTitle = $this->area->title;
        $this->emitShowModal('area');
    }


    public function render()
    {
        $regions = $this->model->regions()->withCount('neighborhoods')->with(['neighborhoods','neighborhoods.areas'])->get();
        return view('livewire.cities.store-city', ['regions' => $regions])->extends('livewire.layouts.admin');
    }
}
