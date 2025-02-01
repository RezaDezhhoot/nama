<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\WrittenRequestStep;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class WrittenRequest extends Model
{
    use SimpleSearchable;

    public array $searchAbleColumns = ['title','id'];
    protected $guarded = ['id'];

    const FILE_LETTER_SUBJECT = 'written_request_letter_image';
    const FILE_SIGN_SUBJECT = 'written_request_sign_image';


    protected $casts = [
        'countable' => 'boolean',
        'step' => WrittenRequestStep::class,
        'status' => RequestStatus::class
    ];

    public function letter(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_LETTER_SUBJECT);
    }

    public function sign(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_SIGN_SUBJECT);
    }

    public function scopeRoleFilter(Builder $builder): Builder
    {
        if (isAdmin()) {
            return $builder;
        }

        return $builder->whereIn('step' , auth()->user()->nama_role->writtenStep());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable')->latest();
    }
}
