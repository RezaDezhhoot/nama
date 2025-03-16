<?php

namespace App\Models;

use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use SimpleSearchable;

    public array $searchAbleColumns = ['title'];

    protected $guarded = ['id'];

    protected $casts = [
        'auto_accept' => 'boolean',
        'type' => UnitType::class,
        'sub_type' => UnitSubType::class
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,'parent_id');
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

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id");
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'unit_id');
    }
}
