<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Exports\RoleExport;
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

    protected $queryString = [
        'search' => [
            'as' => 'search'
        ]
    ];

    public function mount()
    {
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function render()
    {
        $db = config('database.connections.mysql.database');
        $items = User::query()
            ->with(['roles','roles.unit','roles.region'])
            ->whereNotNull('name')
            ->leftJoin(sprintf("%s.user_roles AS  ur",$db),"user_id",'=','users.id')
            ->leftJoin(sprintf("%s.units AS u",$db),'u.id','=','ur.unit_id')
            ->select('ur.role as role2','ur.region_id','ur.unit_id','u.id AS unit_pkey','u.region_id AS unit_region_id','users.*')
            ->when($this->role , function (Builder $builder) {
                switch ($this->role) {
                    case OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES->value:
                    case OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->value:
                    case OperatorRole::MOSQUE_HEAD_COACH->value:
                        $builder->where('ur.role' , $this->role);
                        break;
                    case OperatorRole::AREA_INTERFACE->value:
                    case OperatorRole::MOSQUE_CULTURAL_OFFICER->value:
                        $builder->where(function (Builder $builder) {
                            if ($this->region) {
                                $builder
                                    ->where(function (Builder $builder) {
                                        $builder
                                            ->where('ur.region_id' , $this->region)
                                            ->orWhere('u.region_id' , $this->region);
                                    });
                            } else {
                                $builder
                                    ->where('ur.role' , $this->role);
                            }
                        });
                        break;
                };
            })
            ->when($this->region && ! $this->role , function (Builder $builder){
                $builder->where(function (Builder $builder) {
                    $builder->where("ur.region_id" , $this->region)
                        ->orWhere('u.region_id' , $this->region);
                    ;
                });
            })
            ->when($this->unit , function (Builder $builder){
                $builder->where("ur.unit_id" , $this->unit);
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->groupBy("users.id")
            ->paginate($this->per_page);

        return view('livewire.users.attach-role' , get_defined_vars())->extends('livewire.layouts.admin');
    }

    public function export()
    {
        return (new RoleExport($this->role,$this->region,$this->unit,$this->search))->download('rolex.xlsx');
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
