<?php

namespace App\Models;

use App\Enums\StatisticType;
use App\Traits\SimpleSearchable;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use SimpleSearchable;

    protected $guarded = ['id'];

    public array $searchAbleColumns = ['name','sub_name'];

    protected $table = 'statistic';
    protected $casts = [
        "name" => StatisticType::class,
        'sub_name' => StatisticType::class,
    ];
}
