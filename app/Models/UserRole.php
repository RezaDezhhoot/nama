<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\SchoolCoachType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
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

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
