<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class City extends Model
{
    use SimpleSearchable , Loggable;

    public array $searchAbleColumns = ['title'];

    protected $guarded = ['id'];

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id");
    }

    public function neighborhoods(): HasManyThrough
    {
        return $this->hasManyThrough(Neighborhood::class,Region::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
