<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AreaImport implements ToCollection
{
    public function __construct(public $region)
    {

    }

    public function collection(Collection $rows)
    {
        $this->region->neighborhoods()->insert($rows->map(function ($v)  {
            if ($v[0]) {
                return [
                    'title' => $v[0],
                    'region_id' => $this->region->id
                ];
            }
            return null;
        })->filter()->toArray());
    }
}
