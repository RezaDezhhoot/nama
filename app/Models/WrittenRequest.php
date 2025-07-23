<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\WrittenRequestStep;
use App\Traits\Loggable;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class WrittenRequest extends Model
{
    use SimpleSearchable , Loggable;

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
        return $builder;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable')->latest();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function plan()
    {
        return $this->belongsTo(RequestPlan::class,'request_plan_id')->withTrashed();
    }

    public function scopeItem($q , $id)
    {
        return $q->where('item_id' , $id);
    }

    public function scopeRole(Builder $builder , $role = null): Builder
    {
        return $builder->where(function (Builder $builder) use ($role) {
            $role = OperatorRole::tryFrom($role);
            if ($role && $role !== OperatorRole::MOSQUE_HEAD_COACH) {
                return $builder->whereIn('step' ,$role->step())
                    ->whereHas('unit' , function (Builder $builder) use ($role) {
                        if ($role === OperatorRole::MOSQUE_CULTURAL_OFFICER) {
                            return $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                                $builder->where('role' , $role)->where('user_id' , auth()->id());
                            })->orWhereHas('parent' , function (Builder $builder) use ($role) {
                                $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                                    $builder->where('role' , $role)->where('user_id' , auth()->id());
                                });
                            });
                        }
                        return $builder;
                    });
            }
            return $builder->where('user_id' , auth()->id());
        });
    }
}
