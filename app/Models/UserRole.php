<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\SchoolCoachType;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Tag\B;

class UserRole extends Model
{
    use SoftDeletes , Loggable;

    protected $guarded = ['id'];
    protected $table = 'user_roles';
    protected $connection = 'mysql';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => OperatorRole::class,
            'auto_accept' => 'boolean',
            'ring' => 'boolean',
            'school_coach_type' => SchoolCoachType::class
        ];
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        if ($search) {
            $db = config('database.connections.arman.database');
            $query->join($db.".users as u","u.id",'=','user_roles.user_id')
                ->whereAny(['u.phone','u.name','u.national_id','u.email'],'LIKE','%'.$search.'%')
            ;
        }
        return $query;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class)->withTrashed();
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class,'causer_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by_id');
    }
}
