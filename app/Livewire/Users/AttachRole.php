<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class AttachRole extends BaseComponent
{
    use WithPagination;

    public $role;
    public $users = [] , $item , $region , $unit;

    public function mount()
    {
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function render()
    {
        $db = config('database.connections.mysql.database');
        $items = User::query()
            ->with(['roles','roles.unit'])
//            ->leftJoin(sprintf("%s.user_roles AS  ur",$db),"user_id",'=','users.id')
//            ->select('ur.role as role2','ur.region_id','ur.unit_id','users.*')
//            ->when($this->role , function (Builder $builder) {
//                $builder->where('ur.role' , $this->role);
//            })
//            ->when($this->region , function (Builder $builder){
//                $builder->where("ur.region_id" , $this->region);
//            })
//            ->when($this->unit , function (Builder $builder){
//                $builder->where("ur.unit_id" , $this->unit);
//            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->groupBy("users.id")
            ->paginate($this->per_page);

        return view('livewire.users.attach-role' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function generateToken($id)
    {
        $user = User::query()->find($id);
        if ($user) {
            $token = $user->generateToken();
            $this->dispatch('message',$token);
        }
    }

    public function deleteItem($id)
    {
        User::query()->where('id', $id)->update([
            'nama_role' => null
        ]);
    }


}
