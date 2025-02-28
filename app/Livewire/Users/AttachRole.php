<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class AttachRole extends BaseComponent
{
    use WithPagination;

    public $role;
    public $users = [] , $item;

    public function mount()
    {
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function render()
    {
        $items = User::query()
            ->addSelect([
                'roles_count' => UserRole::query()->selectRaw("COUNT(id)")->whereColumn('users.id','=','user_roles.user_id')
            ])
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->paginate($this->per_page);

        return view('livewire.users.attach-role' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function deleteItem($id)
    {
        User::query()->where('id', $id)->update([
            'nama_role' => null
        ]);
    }


}
