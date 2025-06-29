<?php

namespace App\Console\Commands;

use App\Enums\StatisticType;
use App\Enums\UnitType;
use App\Models\Report;
use App\Models\Request;
use App\Models\Statistic;
use Illuminate\Console\Command;

class StatisticCalculator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:calculator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
            ],[
                'value' => Request::query()->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
            ],[
                'value' => Report::query()->count(),
            ]);

        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_MOSQUE_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::MOSQUE);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_SCHOOL_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::SCHOOL);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_CENTER_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::CENTER);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_UNIVERSITY_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::UNIVERSITY);
                })->count(),
            ]);

        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_MOSQUE_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::MOSQUE);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_SCHOOL_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::SCHOOL);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_CENTER_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::CENTER);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_UNIVERSITY_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::UNIVERSITY);
                })->count(),
            ]);
    }
}
