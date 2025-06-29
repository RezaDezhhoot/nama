<?php

namespace App\Models;

use App\Enums\StatisticType;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $guarded = ['id'];
    protected $table = 'statistic';
    protected $casts = [
        "name" => StatisticType::class,
        'sub_name' => StatisticType::class,
    ];
}
