<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $guarded = ['id'];

    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
