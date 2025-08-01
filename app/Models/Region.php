<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SimpleSearchable , Loggable , SoftDeletes;

    public array $searchAbleColumns = ['title'];

    protected $guarded = ['id'];


    public function neighborhoods(): HasMany
    {
        return $this->hasMany(Neighborhood::class,'region_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class,'city_id')->withTrashed();
    }

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id");
    }
}
