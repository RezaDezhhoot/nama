<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SimpleSearchable , Loggable , SoftDeletes;

    public array $searchAbleColumns = ['title'];

    protected $guarded = ['id'];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class,'neighborhood_id');
    }

    public function scopeSelect2($q)
    {
        return $q->selectRaw("title as text , id");
    }
}
