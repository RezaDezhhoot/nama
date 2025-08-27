<?php

namespace App\Livewire\Users;

use App\Enums\OperatorRole;
use App\Exports\RoleExport;
use App\Livewire\BaseComponent;
use App\Models\DashboardItem;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class AttachRole extends BaseComponent
{
    use WithPagination;

    public $role;
    public $users = [] , $item , $region , $unit;

    public $min_roles , $max_roles;

    protected $queryString = [
        'search' => [
            'as' => 'search'
        ]
    ];

    public function mount()
    {
        $this->authorize('show_roles');
        $this->data['role'] = OperatorRole::labels();
        $this->data['items'] = DashboardItem::query()->pluck('title','id');
    }

    public function render()
    {
        $db = config('database.connections.mysql.database');
        $items = User::query()
            ->with(['roles2','roles2.unit','roles2.region'])
            ->whereNotNull('name')
            ->withCount('roles2')
            ->leftJoin(sprintf("%s.user_roles AS  ur",$db),"user_id",'=','users.id')
            ->leftJoin(sprintf("%s.units AS u",$db),'u.id','=','ur.unit_id')
            ->select('ur.role as role2','ur.region_id','ur.unit_id','u.id AS unit_pkey','u.region_id AS unit_region_id','users.*' ,  DB::raw('COUNT(ur.id) AS roles_count'))
            ->when($this->min_roles , function (Builder $builder) {
                $builder->having('roles_count','>=',$this->min_roles);
            })
            ->when($this->max_roles , function (Builder $builder) {
                $builder->having('roles_count','<=',$this->max_roles);
            })
            ->when($this->role , function (Builder $builder) {
                $builder->where('ur.role' , $this->role);
                if ($this->region) {
                    $builder
                        ->where(function (Builder $builder) {
                            $builder
                                ->where('ur.region_id' , $this->region)
                                ->orWhere('u.region_id' , $this->region);
                        });
                }
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
            ->when($this->item , function (Builder $builder){
                $builder->where("ur.item_id" , $this->item);
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
        $this->authorize('export_roles');
        return (new RoleExport($this->role,$this->region,$this->unit,$this->search , $this->item))->download('rolex.xlsx');
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
        $this->authorize('delete_roles');
        //
    }
}
