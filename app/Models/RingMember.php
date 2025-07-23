<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RingMember extends Model
{
    use SimpleSearchable , SoftDeletes , Loggable;

    protected $guarded = ['id'];

    const FILE_IMAGE_SUBJECT = "image";


    public function ring(): BelongsTo
    {
        return $this->belongsTo(Ring::class,'ring_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_IMAGE_SUBJECT);
    }
}
