<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Livewire\BaseComponent;
use App\Models\User;
use Illuminate\Validation\Rule;

class AttachRole extends BaseComponent
{
    public $role;
    public $users = [];

    public function mount()
    {
        $this->data['role'] = OperatorRole::labels();
    }

    public function render()
    {
        $items = User::query()
            ->panelAccess()
            ->get();

        return view('livewire.users.attach-role' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        User::query()->where('id', $id)->update([
            'nama_role' => null
        ]);
    }

    public function attachRole()
    {
        $this->validate([
            'role' => ['required',Rule::enum(OperatorRole::class)],
            'users' => ['array','min:1'],
            'users.*' => ['exists:arman.users,id']
        ]);
        User::query()->whereIn('id' , $this->users)->update([
            'nama_role' => $this->role
        ]);
        $this->reset(['role','users']);
        $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
    }
}
