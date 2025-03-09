<?php

namespace App\Models;

use App\Enums\RequestPlanStatus;
use App\Enums\RequestPlanVersion;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPlan extends Model
{
    use SoftDeletes , SimpleSearchable;

    public array $searchAbleColumns = ['title','max_number_people_supported','support_for_each_person_amount','body'];

    protected $guarded = ['id'];

    protected $casts = [
        'status' => RequestPlanStatus::class,
        'bold' => 'boolean',
        'version' => RequestPlanVersion::class
    ];

    protected static function booted()
    {
        parent::booted(); // TODO: Change the autogenerated stub
        static::addGlobalScope('pre' , function (Builder $builder) {
            $builder->withCount(['requests' => function ($q) {
                return $q->where('user_id' , auth()->id());
            }]);
        });
    }

    public function scopePublished(Builder $builder): Builder
    {
        return $builder->where('status' , RequestPlanStatus::PUBLISHED);
    }

    public function scopeComingSoon(Builder $builder): Builder
    {
        return $builder->where('status' , RequestPlanStatus::COMING_SOON);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class,'request_plan_id');
    }
}
