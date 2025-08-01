<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Neighborhood extends Model
{
    use SimpleSearchable , Loggable , SoftDeletes;

    public array $searchAbleColumns = ['title'];

    protected $guarded = ['id'];

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id");
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class ,'neighborhood_id');
    }
}
