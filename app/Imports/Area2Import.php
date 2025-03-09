<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Area2Import implements ToCollection
{
    public function __construct(public $neighborhood)
    {

    }

    public function collection(Collection $rows)
    {
        $this->neighborhood->areas()->insert($rows->map(function ($v)  {
            if ($v[0]) {
                return [
                    'title' => $v[0],
                    'neighborhood_id' => $this->neighborhood->id
                ];
            }
           return null;
        })->filter()->toArray());
    }
}
