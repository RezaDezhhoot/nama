<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestPlanLimit extends Model
{
    use Loggable , SimpleSearchable;

    public array $searchAbleColumns = ['value'];
    protected $guarded = ['id'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(RequestPlan::class,'request_plan_id');
    }
}
