<?php

namespace App\Livewire\Dashboards;

use App\Livewire\BaseComponent;
use App\Models\Report;
use App\Models\Request;
use App\Models\Statistic;
use Carbon\CarbonPeriod;
use Morilog\Jalali\Jalalian;

class Dashboard extends BaseComponent
{
    public array $box = [];
    public $from_date , $to_date;

    public function mount()
    {
        dd(auth()->user()->unitIds());
        $this->from_date = dateConverter(now()->subDays(15)->startOfDay()->format('Y-m-d H:i:s'),'j','Y-m-d H:i:s');
        $this->to_date = dateConverter(now()->endOfDay()->format('Y-m-d H:i:s'),'j','Y-m-d H:i:s');

        $this->box = [
            'root' => Statistic::query()->whereNull('sub_name')->get()->pluck("value","name.value")->toArray(),
            'sub' => Statistic::query()->whereNotNull('sub_name')->get()->pluck("value","sub_name.value")->toArray(),
        ];

//        dd($this->box);
    }

    public function init(): void
    {
        $this->disableLoader();
        $this->runFilterableCharts();
    }

    public function runFilterableCharts(): void
    {
        [$from , $to] = $this->getDates();
        $this->loadFilterableUsersCharts('DataChart' , [
            "requests" => Request::query()->select(["id",'created_at'])->whereBetween("created_at",[$from,$to])->get() ,
            "reports" => Report::query()->select(["id","created_at"])->whereBetween("created_at",[$from,$to])->get()
        ]);
    }

    private function loadFilterableUsersCharts($chartName , array $rawData): void
    {
        $labels = [] ; $data = [];
        [$from , $to] = $this->getDates();
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $value) {
            $v = $value->format('Y-m-d');
            $labels[$v] = dateConverter(date: $v , toFormat: "Y-m-d");
        }


        foreach ($rawData as $name => $item) {
            $groupedData = collect($item)->sortBy('created_at')->groupBy('date')->toArray();
            foreach ($labels as $k => $label){
                $data[$name][] = sizeof($groupedData[$k] ?? []);
            }
        }

        if (sizeof($data) > 0 && sizeof($labels) > 0) {
            $chartData = [
                'data' => $data,
                'labels' => array_values($labels)
            ];
            $this->dispatch($chartName , $chartData);
        }
    }

    private function getDates(): array
    {
        return [$this->getCld($this->from_date) , $this->getCld($this->to_date)];
    }

    private function getCld($date)
    {
        return Jalalian::fromFormat("Y-m-d H:i:s", convert2english($date))->toCarbon();
    }

    public function render()
    {
        return view('livewire.dashboards.dashboard')->extends('livewire.layouts.admin');
    }
}
