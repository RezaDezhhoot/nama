<?php

namespace App\Exports;

use App\Models\Ring;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RingExport implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize,WithDrawings , WithDefaultStyles
{
    use Exportable;

    public function __construct(public $owner = null , public $type = null  , public $id = null , public $q = null)
    {

    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => false,
            ],
        ]);
    }


    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
//        $drawing->setPath(public_path('/img/logo.jpg'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('B3');

        return $drawing;
    }

    public function query()
    {
        return Ring::query()
            ->latest()
            ->with(['image','members','members.image'])
            ->when($this->owner , function (Builder $builder){
                $builder->where('owner_id' , $this->owner);
            })
            ->when($this->type , function (Builder $builder) {
                $builder->whereHas('item' , function (Builder $builder){
                   $builder->where('type' , $this->type);
                });
            })
            ->when($this->q , function (Builder $builder) {
                $builder->search($this->q);
            })
            ->when($this->id , function (Builder $builder) {
                $builder->where('id' , $this->id);
            });
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function headings(): array
    {
        return [
            'شناسه',
            'نوع ردیف',
            'نام',
            'کد ملی',
            'تاریخ تولد-میلادی',
            'تاریخ تولد-شمسی',
            'کد پستی',
            'آدرس',
            'شماره تلفن',
            'تصویر پرسنلی',
            'تاریخ ثبت',
            'تاریخ آخرین بروزرسانی',
            'نام پدر',
            'میزان تحصیلات',
            'رشته تحصیلی',
            'حوزه عملکردی',
            'حوزه مهارتی',
            'توضیحات',
            'شغل',
            'شماره شبا',
        ];
    }

    public function prepareRows($rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row->id,
                'type' => 'مربی حلقه',
                'name' => $row->name,
                'national_code' => $row->national_code,
                'birthdate' => $row->birthdate,
                'birthdate_j' => dateConverter($row->birthdate),
                'postal_code' => $row->postal_code,
                'address' => $row->address,
                'phone' => $row->phone,
                'image' => $row->image?->url,
                'created_at' => persian_date($row->created_at),
                'updated_at' => persian_date($row->updated_at),
                'father_name' => 'none',
                'level_of_education' => $row->level_of_education,
                'field_of_study' => $row->field_of_study,
                'functional_area' => $row->functional_area ? json_encode($row->functional_area , JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE) : null,
                'skill_area' => $row->skill_area ? json_encode($row->skill_area , JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE) : null,
                'description' => $row->description,
                'job' => $row->job,
                'sheba_number' => $row->sheba_number,
            ];
            foreach ($row->members as $member) {
                $data[] = [
                    'id' => $member->id,
                    'type' => 'عضو حلقه',
                    'name' => $member->name,
                    'national_code' => $member->national_code,
                    'birthdate' => $member->birthdate,
                    'birthdate_j' => dateConverter($member->birthdate),
                    'postal_code' => $member->postal_code,
                    'address' => $member->address,
                    'phone' => $member->phone,
                    'image' => $member->image?->url,
                    'created_at' => persian_date($member->created_at),
                    'updated_at' => persian_date($member->updated_at),
                    'father_name' => $member->father_name,
                    'level_of_education' => 'none',
                    'field_of_study' => 'none',
                    'functional_area' => 'none',
                    'skill_area' => 'none',
                    'description' => 'none',
                    'job' => 'none',
                    'sheba_number' => 'none',
                ];
            }
        }
        return $data;
    }
}
