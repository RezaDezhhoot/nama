<?php

namespace App\Exports;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportUnits implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;
    public function __construct(public $type)
    {
    }

    public function query()
    {
        return Unit::query()->with(['city','region','area','parent','neighborhood'])->when($this->type , function ($q){
            $q->where('type' , $this->type);
        });
    }

    public function headingRow(): int
    {
        return 1;
    }
    public function headings(): array
    {
        return [
            'ID',
            'عنوان',
            'نوع',
            'نوع فرعی',
            'مسجد محوری',
            'شهر',
            'منظقه',
            'محله',
            'ناحیه',
            'lat',
            'lng',
            'کد یکتای واحد',
            'شماره 1',
            'شماره 2',
            'شماره 3',
            'شماره 4',
            'شماره 5',
            'شماره 6',
            'شماره 7',
            'شماره 8',
        ];
    }
    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            return [
                'id' => $row->id,
                'title' => $row->title,
                'type' => $row->type?->label(),
                'sub_type' => $row->sub_type?->label(),
                'parent_id' => $row->parent?->title ?? '-',
                'city' => $row->city?->title,
                'region' => $row->region?->title,
                'neighborhood' => $row->neighborhood?->title,
                'area' => $row->area?->title,
                'lat' => $row->lat,
                'lng' => $row->lng,
                'code' => $row->code,
                'phone1' => $row->phone1_title.' : '.$row->phone1,
                'phone2' => $row->phone2_title.' : '.$row->phone2,
                'phone3' => $row->phone3_title.' : '.$row->phone3,
                'phone4' => $row->phone4_title.' : '.$row->phone4,
                'phone5' => $row->phone5_title.' : '.$row->phone5,
                'phone6' => $row->phone6_title.' : '.$row->phone6,
                'phone7' => $row->phone7_title.' : '.$row->phone7,
                'phone8' => $row->phone8_title.' : '.$row->phone8,
            ];
        });
    }
}
