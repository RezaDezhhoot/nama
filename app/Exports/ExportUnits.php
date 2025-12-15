<?php

namespace App\Exports;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportUnits implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;
    public function __construct(public $type= null , public $region = null , public $unit = null)
    {
    }

    public function query()
    {
        return Unit::query()->with(['city','region','area','state','parent','neighborhood'])->when($this->region , function (Builder $builder) {
            $builder->where('region_id' , $this->region);
        })->when($this->unit , function (Builder $builder) {
            $builder->where('parent_id' , $this->unit);
        })->when($this->type , function ($q){
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
            'استان',
            'شهر',
            'منطقه',
            'محله',
            'ناحیه',
            'کد پستی',
            'lat',
            'lng',
            'شماره 1',
            'شماره 2',
            'شماره 3',
            'شماره 4',
            'شماره 5',
            'شماره 6',
            'شماره 7',
            'شماره 8',
            'مرکز آرمانی',
            'شناسه یکتای واحد(سیستمی)',
            'شناسه یکتای واحد',
            'نام مسئول',
            'شماره مسئول',
            'جنسیت',
            'شماره تماس مرکز',
            'حوزه ی فعالیت های مرکز',
            'محدوده سنی'
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
                'state' => $row->state?->title,
                'city' => $row->city?->title,
                'region' => $row->region?->title,
                'neighborhood' => $row->neighborhood?->title,
                'postal_code' => $row->postal_code,
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
                'armani' => $row->armani ? 'بله' : 'خیر',
                'systematic_code' => $row->systematic_code,
                'responsible' => $row->responsible,
                'responsible_phone' => $row->responsible_phone,
                'gender' => $row->gender?->label(),
                'tell' => $row->tell,
                'scope_activity' => $row->scope_activity,
                'range' => $row->from_age.' - '.$row->to_age,
            ];
        });
    }
}
