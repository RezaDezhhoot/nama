<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Enums\SchoolCoachType;
use App\Enums\UnitType;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Validation\Rule;

class StoreUser extends BaseComponent
{
    public $user;
    public $item , $role;

    public $unit , $main_unit;

    public $city , $region , $neighborhood , $area , $auto_accept , $lat , $lng;
    public $regionAjax , $neighborhoodAjax , $areaAjax , $coach_type;

    public $sheba1 , $sheba1_title;
    public $sheba2 , $sheba2_title;
    public $sheba3 , $sheba3_title;
    public $sheba4 , $sheba4_title;
    public $sheba5 , $sheba5_title;
    public $sheba6 , $sheba6_title;
    public $sheba7 , $sheba7_title;
    public $sheba8 , $sheba8_title;

    public $roleToEdit;

    public function mount($action , $id)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->user = User::query()->findOrFail($id);
            $this->header = $this->user->name;
        } else abort(404);
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
        $this->data['main_units'] = Unit::query()->whereNull('parent_id')->latest()->get()->pluck('full','id');
        $this->data['units'] = Unit::query()->whereNotNull('parent_id')->latest()->get()->pluck('full','id');
        $this->item = collect($this->data['items'])->keys()->first();
        $this->data['coach_type'] = SchoolCoachType::labels();
    }


    public function updatedRole()
    {
        $this->reset(['city','region','neighborhood','area','lat','lng']);
    }

    public function render()
    {
        $roles = UserRole::query()->with(['unit'])
            ->with(['city','region','neighborhood','area'])
            ->where('user_id',$this->user->id)->get()->groupBy('item_id');

        if ($this->item) {
            $itemModel = DashboardItem::query()->findOrFail($this->item);
            $this->data['units'] = Unit::query()
                ->when($this->main_unit , function ($q) {
                    $q->where('parent_id' , $this->main_unit);
                })
                ->whereNotNull('parent_id')
                ->where('type',$itemModel->type)
                ->latest()->get()->pluck('full','id');
        } else {
            $this->data['units'] = [];
        }


        return view('livewire.users.store-user' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function attachRole()
    {
        $itemModel  = DashboardItem::query()->findOrFail($this->item);
        $this->validate([
            'role' => ['required',Rule::enum(OperatorRole::class)],
            'item' => ['required'],
            'unit' => [in_array($this->role,[OperatorRole::MOSQUE_HEAD_COACH->value,OperatorRole::MOSQUE_CULTURAL_OFFICER->value]) ? 'required' : 'nullable'],
            'coach_type' => [($this->role == OperatorRole::MOSQUE_HEAD_COACH->value && $itemModel->type === UnitType::SCHOOL) ? 'required' : 'nullable','string','max:150'],
            'city' => [$this->role == OperatorRole::AREA_INTERFACE->value ? 'required' : 'nullable'],
            'region' => [$this->role == OperatorRole::AREA_INTERFACE->value ? 'required' : 'nullable'],
            'neighborhood' => [ 'nullable'],
            'area' => ['nullable'],
            'auto_accept' => ['nullable','boolean'],
            'sheba1' => ['nullable','string','max:100'],
            'sheba1_title' => ['nullable','string','max:100'],
            'sheba2' => ['nullable','string','max:100'],
            'sheba2_title' => ['nullable','string','max:100'],
            'sheba3' => ['nullable','string','max:100'],
            'sheba3_title' => ['nullable','string','max:100'],
            'sheba4' => ['nullable','string','max:100'],
            'sheba4_title' => ['nullable','string','max:100'],
            'sheba5' => ['nullable','string','max:100'],
            'sheba5_title' => ['nullable','string','max:100'],
            'sheba6' => ['nullable','string','max:100'],
            'sheba6_title' => ['nullable','string','max:100'],
            'sheba7' => ['nullable','string','max:100'],
            'sheba7_title' => ['nullable','string','max:100'],
            'sheba8' => ['nullable','string','max:100'],
            'sheba8_title' => ['nullable','string','max:100'],
        ]);
        if ($this->role == OperatorRole::MOSQUE_HEAD_COACH->value && UserRole::query()->where(
            [
                ['user_id' , $this->user->id],
                ['item_id' , $this->item],
                ['role' , OperatorRole::MOSQUE_HEAD_COACH->value]
            ])->exists()
        ) {
            $this->emitNotify("این نقش قبلا به کاربر متصل شده است",'warning');
        } else {
            $this->user->roles()->create([
                'item_id' => $this->item,
                'role' => $this->role,
                'user_id' => $this->user->id,
                'unit_id' => emptyToNull($this->unit),
                'auto_accept' => emptyToNull($this->auto_accept) ?? false,
                'lat' => $this->lat,
                'lng' => $this->lng,
                'city_id' => emptyToNull($this->city),
                'region_id' => emptyToNull($this->region),
                'neighborhood_id' => emptyToNull($this->neighborhood),
                'area_id' => emptyToNull($this->area),
                'school_coach_type' => emptyToNull($this->coach_type),
                'sheba1' => $this->sheba1,
                'sheba1_title' => $this->sheba1_title,
                'sheba2' => $this->sheba2,
                'sheba2_title' => $this->sheba2_title,
                'sheba3' => $this->sheba3,
                'sheba3_title' => $this->sheba3_title,
                'sheba4' => $this->sheba4,
                'sheba4_title' => $this->sheba4_title,
                'sheba5' => $this->sheba5,
                'sheba5_title' => $this->sheba5_title,
                'sheba6' => $this->sheba6,
                'sheba6_title' => $this->sheba6_title,
                'sheba7' => $this->sheba7,
                'sheba7_title' => $this->sheba7_title,
                'sheba8' => $this->sheba8,
                'sheba8_title' => $this->sheba8_title,
            ]);
            $this->reset(['role','unit','sheba1','sheba1_title',
                'sheba2','sheba2_title',
                'sheba3','sheba3_title',
                'sheba4','sheba4_title',
                'sheba5','sheba5_title',
                'sheba6','sheba6_title',
                'sheba7','sheba7_title',
                'sheba8','sheba8_title','lat','lng']);
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        }
    }

    public function deleteRole($id)
    {
        UserRole::destroy($id);
    }

    public function updatedCity($v): void
    {
        $this->regionAjax = route('admin.feed.regions',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#region' , $this->regionAjax);
        $this->dispatch('reloadAjaxURL#edit_region' , $this->regionAjax);
    }

    public function updatedRegion($v): void
    {
        $this->neighborhoodAjax = route('admin.feed.neighborhoods',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#neighborhood' , $this->neighborhoodAjax);
        $this->dispatch('reloadAjaxURL#edit_neighborhood' , $this->neighborhoodAjax);
    }

    public function updatedNeighborhood($v): void
    {
        $this->areaAjax = route('admin.feed.areas',is_array($v) ? $v['id'] : $v);
        $this->dispatch('reloadAjaxURL#area' , $this->areaAjax);
        $this->dispatch('reloadAjaxURL#edit_area' , $this->areaAjax);
    }

    public function editRole($id)
    {
        $this->roleToEdit = UserRole::query()->with(['city','region','neighborhood','area','unit','unit.parent'])->findOrFail($id);

        $this->role = $this->roleToEdit->role?->value;
        $this->unit = $this->roleToEdit->unit_id;
        $this->lat = $this->roleToEdit->lat;
        $this->lng = $this->roleToEdit->lng;

        $this->city = $this->roleToEdit->city?->toArray();
        $this->region = $this->roleToEdit->region?->toArray();
        $this->neighborhood = $this->roleToEdit->neighborhood?->toArray();
        $this->area = $this->roleToEdit->area?->toArray();
        $this->coach_type = $this->roleToEdit->school_coach_type?->value;

        $this->sheba1 = $this->roleToEdit->sheba1;
        $this->sheba1_title = $this->roleToEdit->sheba1_title;
        $this->sheba2 = $this->roleToEdit->sheba2;
        $this->sheba2_title = $this->roleToEdit->sheba2_title;
        $this->sheba3 = $this->roleToEdit->sheba3;
        $this->sheba3_title = $this->roleToEdit->sheba3_title;
        $this->sheba4 = $this->roleToEdit->sheba4;
        $this->sheba4_title = $this->roleToEdit->sheba4_title;
        $this->sheba5 = $this->roleToEdit->sheba5;
        $this->sheba5_title = $this->roleToEdit->sheba5_title;
        $this->sheba6 = $this->roleToEdit->sheba6;
        $this->sheba6_title = $this->roleToEdit->sheba6_title;
        $this->sheba7 = $this->roleToEdit->sheba7;
        $this->sheba7_title = $this->roleToEdit->sheba7_title;
        $this->sheba8 = $this->roleToEdit->sheba8;
        $this->sheba8_title = $this->roleToEdit->sheba8_title;

        $this->updatedCity($this->roleToEdit->city_id);
        $this->updatedRegion($this->roleToEdit->region_id);
        $this->updatedNeighborhood($this->roleToEdit->neighborhood_id);

        $this->emitShowModal("role");

        $this->dispatch('reloadSelect2#edit_city',$this->city);
        $this->dispatch('reloadSelect2#edit_region',$this->region);
        $this->dispatch('reloadSelect2#edit_neighborhood',$this->neighborhood);
        $this->dispatch('reloadSelect2#edit_area',$this->area);
        if (\App\Enums\OperatorRole::MOSQUE_CULTURAL_OFFICER->value == $this->role) {
            $this->dispatch('reloadSelect2#edit_unit',$this->roleToEdit->unit?->toArray());
        } elseif ($this->role == \App\Enums\OperatorRole::MOSQUE_HEAD_COACH->value) {
            $this->dispatch('reloadSelect2#edit_main_unit',$this->roleToEdit->unit?->parent?->toArray());
        }
    }

    public function updateRole()
    {
        $itemModel  = DashboardItem::query()->findOrFail($this->item);
        $this->validate([
            'unit' => [in_array($this->role,[OperatorRole::MOSQUE_HEAD_COACH->value,OperatorRole::MOSQUE_CULTURAL_OFFICER->value]) ? 'required' : 'nullable'],
            'coach_type' => [($this->role == OperatorRole::MOSQUE_HEAD_COACH->value && $itemModel->type === UnitType::SCHOOL) ? 'required' : 'nullable','string','max:150'],
            'city' => [$this->role == OperatorRole::AREA_INTERFACE->value ? 'required' : 'nullable'],
            'region' => [$this->role == OperatorRole::AREA_INTERFACE->value ? 'required' : 'nullable'],
            'neighborhood' => [ 'nullable'],
            'area' => ['nullable'],
            'auto_accept' => ['nullable','boolean'],
            'sheba1' => ['nullable','string','max:100'],
            'sheba1_title' => ['nullable','string','max:100'],
            'sheba2' => ['nullable','string','max:100'],
            'sheba2_title' => ['nullable','string','max:100'],
            'sheba3' => ['nullable','string','max:100'],
            'sheba3_title' => ['nullable','string','max:100'],
            'sheba4' => ['nullable','string','max:100'],
            'sheba4_title' => ['nullable','string','max:100'],
            'sheba5' => ['nullable','string','max:100'],
            'sheba5_title' => ['nullable','string','max:100'],
            'sheba6' => ['nullable','string','max:100'],
            'sheba6_title' => ['nullable','string','max:100'],
            'sheba7' => ['nullable','string','max:100'],
            'sheba7_title' => ['nullable','string','max:100'],
            'sheba8' => ['nullable','string','max:100'],
            'sheba8_title' => ['nullable','string','max:100'],
        ]);
        $this->roleToEdit->fill([
            'unit_id' => emptyToNull($this->unit),
            'lat' => $this->lat,
            'lng' => $this->lng,
            'city_id' => emptyToNull($this->city),
            'region_id' => emptyToNull($this->region),
            'neighborhood_id' => emptyToNull($this->neighborhood),
            'area_id' => emptyToNull($this->area),
            'school_coach_type' => emptyToNull($this->coach_type),
            'sheba1' => $this->sheba1,
            'sheba1_title' => $this->sheba1_title,
            'sheba2' => $this->sheba2,
            'sheba2_title' => $this->sheba2_title,
            'sheba3' => $this->sheba3,
            'sheba3_title' => $this->sheba3_title,
            'sheba4' => $this->sheba4,
            'sheba4_title' => $this->sheba4_title,
            'sheba5' => $this->sheba5,
            'sheba5_title' => $this->sheba5_title,
            'sheba6' => $this->sheba6,
            'sheba6_title' => $this->sheba6_title,
            'sheba7' => $this->sheba7,
            'sheba7_title' => $this->sheba7_title,
            'sheba8' => $this->sheba8,
            'sheba8_title' => $this->sheba8_title,
        ])->save();
        $this->reset(['role','unit',
            'sheba1','sheba1_title',
            'sheba2','sheba2_title',
            'sheba3','sheba3_title',
            'sheba4','sheba4_title',
            'sheba5','sheba5_title',
            'sheba6','sheba6_title',
            'sheba7','sheba7_title',
            'sheba8','sheba8_title','lat','lng']);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        $this->emitHideModal("role");
    }
}
