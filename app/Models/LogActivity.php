<?php

namespace App\Models;

use App\Enums\Events;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity;

class LogActivity extends Activity
{
    use SimpleSearchable;

    public array $searchAbleColumns = ['id'];

    protected $casts = [
        'event' => Events::class,
        'properties' => "array"
    ];

    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
