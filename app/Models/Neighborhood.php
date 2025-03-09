<?php

namespace App\Models;

use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Neighborhood extends Model
{
    use SimpleSearchable;

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
