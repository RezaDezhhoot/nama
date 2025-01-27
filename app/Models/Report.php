<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Report extends Model
{
    protected $casts = [
        'confirm' => 'boolean',
        'step' => RequestStep::class,
        'status' => RequestStatus::class
    ];

    const FILE_IMAGES_SUBJECT = 'report_images';
    const FILE_VIDEOS_SUBJECT = 'report_videos';

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class,'request_id')->withTrashed();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(File::class,'fileable')->subject(self::FILE_IMAGES_SUBJECT);
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(File::class,'fileable')->subject(self::FILE_VIDEOS_SUBJECT);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable');
    }
}
