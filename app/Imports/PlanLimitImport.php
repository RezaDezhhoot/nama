<?php

namespace App\Imports;

use App\Models\RequestPlan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanLimitImport implements ToCollection
{
    public function __construct(protected RequestPlan $model)
    {

    }

    public function collection(Collection $rows)
    {
        $data = $rows->filter(function ($v) {
            return ! empty($v[0]);
        })->map(function ($v) {
            return [
                'value' => convert2english(trim($v[0])),
                'request_plan_id' => $this->model->id
            ];
        })->toArray();
        if (sizeof($data) > 0) {
            $this->model->limits()->insert($data);
        }
    }
}
