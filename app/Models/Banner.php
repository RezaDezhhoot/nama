<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use SimpleSearchable , Loggable;

    public $searchAbleColumns = ['title'];
    protected $guarded = ['id'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }
}
