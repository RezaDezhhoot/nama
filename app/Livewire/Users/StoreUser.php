<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Validation\Rule;

class StoreUser extends BaseComponent
{
    public $user;
    public $item , $role;

    public function mount($action , $id)
    {
        $this->setMode($action);
        if ($this->isUpdatingMode()) {
            $this->user = User::query()->findOrFail($id);
            $this->header = $this->user->name;
        } else abort(404);
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }


    public function render()
    {
        $roles = UserRole::query()->where('user_id',$this->user->id)->get()->groupBy('item_id');
        return view('livewire.users.store-user' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function attachRole()
    {
        $this->validate([
            'role' => ['required',Rule::enum(OperatorRole::class)],
            'item' => ['required']
        ]);
        $this->user->roles()->create([
            'item_id' => $this->item,
            'role' => $this->role,
            'user_id' => $this->user->id
        ]);
        $this->reset(['role']);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
    }

    public function deleteRole($id)
    {
        UserRole::destroy($id);
    }
}
