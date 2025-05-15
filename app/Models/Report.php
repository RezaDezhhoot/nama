<?php

namespace App\Models;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use function Symfony\Component\Translation\t;

class Report extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'confirm' => 'boolean',
        'step' => RequestStep::class,
        'last_updated_by' => RequestStep::class,
        'status' => RequestStatus::class,
        'messages' => 'array',
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

    public function video(): MorphOne
    {
        return $this->morphOne(File::class,'fileable')->subject(self::FILE_VIDEOS_SUBJECT);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class,'fileable');
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
        return $builder;
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class,'commentable');
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
                return $builder->where(function (Builder $builder) use ($role) {
                    if (in_array($role,[OperatorRole::MOSQUE_CULTURAL_OFFICER,OperatorRole::AREA_INTERFACE]) || request()->filled('status')) {
                        $builder->whereIn('step' ,$role->step())->orWhereIn('step',$role->history());
                    }
                })->whereHas('request' , function (Builder $builder) use ($role) {
                    $builder->whereHas('unit' , function (Builder $builder) use ($role) {
                        if ($role === OperatorRole::MOSQUE_CULTURAL_OFFICER) {
                            return $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                                $builder->where('role' , $role)->where('user_id' , auth()->id());
                            })->orWhereHas('parent' , function (Builder $builder) use ($role) {
                                $builder->whereHas('roles' , function (Builder $builder) use ($role) {
                                    $builder->where('role' , $role)->where('user_id' , auth()->id());
                                });
                            });
                        } elseif ($role === OperatorRole::AREA_INTERFACE) {
                            [$cities , $regions] = auth()->user()->getAreaInterfaceLocations();
                            $builder
                                ->whereIn('city_id' , $cities)
                                ->whereIn('region_id' , $regions)
                            ;
                        }
                        return $builder;
                    });
                });

            }
            return $builder->whereHas('request' , function ($q) {
                $q->where('user_id',auth()->id());
            });
        });
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class,'item_id');
    }
}
