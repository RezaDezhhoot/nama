<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes , SimpleSearchable;

    public array $searchAbleColumns = ['id','body'];
    protected $guarded = ['id'];

    const FILE_IMAM_LETTER_SUBJECT = 'request_imam_letter';
    const FILE_AREA_INTERFACE_LETTER_SUBJECT = 'request_area_interface_letter';

    protected $casts = [
        'confirm' => 'boolean',
        'status' => RequestStatus::class,
        'step' => RequestStep::class,
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(RequestPlan::class,'request_plan_id')->withTrashed();
    }

    public function imamLetter(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_IMAM_LETTER_SUBJECT);
    }

    public function areaInterfaceLetter(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_AREA_INTERFACE_LETTER_SUBJECT);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable')->latest();
    }

    public function scopeConfirmed(Builder $builder): Builder
    {
        return $builder->where('confirm' , true);
    }

    public function scopeRoleFilter(Builder $builder): Builder
    {
        if (isAdmin()) {
            return $builder;
        }

        return $builder->whereIn('step' , auth()->user()->nama_role->step());
    }
}
