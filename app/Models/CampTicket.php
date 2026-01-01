<?php

namespace App\Models;

use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampTicket extends Model
{
    use SimpleSearchable;
    protected array $searchAbleColumns = ['request_id'];
    protected $guarded = ['id'];

    protected $casts = [
        'result' => 'array'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class,'request_id')->withTrashed();
    }
}
