<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
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

    public function mount($action , $id)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->user = User::query()->findOrFail($id);
            $this->header = $this->user->name;
        } else abort(404);
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
        $this->data['main_units'] = Unit::query()->whereNull('parent_id')->latest()->pluck('title','id');
        $this->data['units'] = Unit::query()->whereNotNull('parent_id')->latest()->pluck('title','id');
    }


    public function render()
    {
        $roles = UserRole::query()->with(['unit'])->where('user_id',$this->user->id)->get()->groupBy('item_id');

        if ($this->item) {
            $item = DashboardItem::query()->findOrFail($this->item);
            $this->data['units'] = Unit::query()
                ->when($this->main_unit , function ($q) {
                    $q->where('parent_id' , $this->main_unit);
                })
                ->whereNotNull('parent_id')
                ->where('type',$item->type)
                ->latest()->pluck('title','id');
        } else {
            $this->data['units'] = [];
        }


        return view('livewire.users.store-user' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function attachRole()
    {
        $this->validate([
            'role' => ['required',Rule::enum(OperatorRole::class)],
            'item' => ['required'],
            'unit' => [in_array($this->role,[OperatorRole::MOSQUE_HEAD_COACH->value,OperatorRole::MOSQUE_CULTURAL_OFFICER->value]) ? 'required' : 'nullable','string','max:150']
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
                'unit_id' => emptyToNull($this->unit)
            ]);
            $this->reset(['role','item','unit']);
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
        }
    }

    public function deleteRole($id)
    {
        UserRole::destroy($id);
    }
}
