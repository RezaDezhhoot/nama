<?php

namespace App\Livewire\Users;

use App\Livewire\BaseComponent;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class StorePermissions extends BaseComponent
{
    public $selectedPermissions = [];
    public $user;
    public $roles = [];

    public function mount($id)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin','administrator']),403);
        $this->user = User::query()->with(['roles','permissions'])->findOrFail($id);
        if ($this->user->hasRole('administrator')) {
            abort(403);
        }
        if ($this->user->hasRole('super_admin') && ! auth()->user()->hasRole('administrator')) {
            abort(403);
        }
        $this->header = $this->user->name;
        $this->data['permissions'] = Permission::query()->pluck('title','id');
        $this->selectedPermissions = $this->user->permissions->pluck('id')->toArray();
    }

    public function store()
    {
        $this->validate([
            'selectedPermissions' => ['nullable','array','min:0'],
            'selectedPermissions.*' => ['required',Rule::exists('permissions','id')],
            'roles' => ['nullable','array','min:0'],
            'roles.*' => ['required',Rule::exists('roles','id')]
        ]);
        try {
            DB::beginTransaction();
            $this->user->roles()->sync($this->roles);
            $this->user->permissions()->sync($this->selectedPermissions);
            $this->emitNotify('اطلاعات با موفقیت ذخیره شد');
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->emitNotify('مشکلی در حین ذخیره اطلاعات پیش آمده','warning');
        }
    }

    public function render()
    {
        return view('livewire.users.store-permissions')->extends('livewire.layouts.admin');
    }
}
