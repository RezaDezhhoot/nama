<?php

namespace App\Models;

use App\Enums\RequestPlanStatus;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPlan extends Model
{
    use SoftDeletes , SimpleSearchable;

    public array $searchAbleColumns = ['title','max_number_people_supported','support_for_each_person_amount','body'];

    protected $guarded = ['id'];

    protected $casts = [
        'status' => RequestPlanStatus::class,
        'bold' => 'boolean'
    ];

    public function scopePublished(Builder $builder): Builder
    {
        return $builder->where('status' , RequestPlanStatus::PUBLISHED);
    }

    public function scopeComingSoon(Builder $builder): Builder
    {
        return $builder->where('status' , RequestPlanStatus::COMING_SOON);
    }
}
