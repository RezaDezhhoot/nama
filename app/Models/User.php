<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\OperatorRole;
use App\Enums\UserRole;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable , HasApiTokens , SimpleSearchable;

    protected $connection = 'arman';

    protected $guarded = ['id'];

    public array $searchAbleColumns = ['name','phone','email','national_id'];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'nama_role' => OperatorRole::class
        ];
    }

    public function scopePanelAccess(Builder $builder): Builder
    {
        return $builder->whereIn('role',[UserRole::SUPER_ADMIN->value , UserRole::ADMIN->value])
            ->orWhereIn('nama_role',OperatorRole::values());
    }

    public function scopeSelect2($q)
    {
        return $q->selectRaw("CONCAT(name,' - کدملی: ',national_id, ' - شماره همراه:  ',phone) as text , id");
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\UserRole::class,'user_id');
    }
}
