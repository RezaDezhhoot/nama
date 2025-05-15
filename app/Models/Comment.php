<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
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
