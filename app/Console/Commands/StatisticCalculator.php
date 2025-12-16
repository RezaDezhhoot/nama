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
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_GARDEN_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::GARDEN);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_HALL_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::HALL);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_STADIUM_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::STADIUM);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_DARUL_QURAN_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::DARUL_QURAN);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_CULTURAL_INSTITUTE_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::CULTURAL_INSTITUTE);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_SEMINARY_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::SEMINARY);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REQUESTS,
                "sub_name" => StatisticType::TOTAL_QURANIC_CENTER_REQUESTS
            ],[
                "value" => Request::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::QURANIC_CENTER);
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
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_GARDEN_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::GARDEN);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_HALL_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::HALL);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_STADIUM_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::STADIUM);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_DARUL_QURAN_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::DARUL_QURAN);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_CULTURAL_INSTITUTE_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::CULTURAL_INSTITUTE);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_SEMINARY_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::SEMINARY);
                })->count(),
            ]);
        Statistic::query()
            ->updateOrCreate([
                "name" => StatisticType::TOTAL_REPORTS,
                "sub_name" => StatisticType::TOTAL_QURANIC_CENTER_REPORTS
            ],[
                "value" => Report::query()->whereHas('item' , function ($q) {
                    $q->where('type' , UnitType::QURANIC_CENTER);
                })->count(),
            ]);

    }
}
