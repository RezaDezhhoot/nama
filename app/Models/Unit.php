<?php

namespace App\Models;

use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function title(): Attribute
    {
        return Attribute::get(function ($v) {
           return sprintf("%s(%s) - %s",$v,$this->parent_id ? "معمولی" : 'محوری',$this->sub_type instanceof UnitSubType ? $this->sub_type?->label() : UnitSubType::tryFrom($this->sub_type)?->label());
        });
    }


    public function text(): Attribute
    {
        return Attribute::get(function ($v) {
            return sprintf("%s(%s) - %s",$v,$this->parent_id ? "معمولی" : 'محوری',$this->sub_type instanceof UnitSubType ? $this->sub_type?->label() : UnitSubType::tryFrom($this->sub_type)?->label());
        });
    }

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
        return $q->selectRaw("title as text , id,sub_type,parent_id");
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'unit_id');
    }
}
