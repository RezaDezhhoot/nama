<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use Loggable;

    public function scopeSearch(Builder $query, $search): Builder
    {
        if ($search) {
            $query->whereHasMorph('commentable',[Request::class,Report::class] , function (Builder $builder) use ($search) {
                $builder->search($search);
            });
        }
        return $query;
    }

    protected $guarded = ['id'];

    protected $casts = [
        'from_status' => RequestStatus::class,
        'to_status' => RequestStatus::class,
        'step' => RequestStep::class
    ];
    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
