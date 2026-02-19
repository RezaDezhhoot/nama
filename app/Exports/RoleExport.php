<?php

namespace App\Exports;

use App\Enums\OperatorRole;
use App\Models\Ring;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RoleExport implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize,WithDrawings , WithDefaultStyles
{
    use Exportable;

    public function __construct(public $role = null , public $region = null  , public $unit = null , public $search  = null , public $item = null)
    {
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '-1');
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
        $db = config('database.connections.mysql.database');
        return UserRole::query()
            ->latest('user_roles.user_id')
            ->select("u.id","u.name","u.phone","u.national_id","user_roles.*")
            ->with(['item','city','region','neighborhood','unit','unit.parent'])
            ->join($db.".users as u","u.id",'=','user_roles.user_id')
            ->when($this->unit , function (Builder $builder) {
                $builder->where('unit_id' , $this->unit);
            })->when($this->search , function (Builder $builder) {
                $builder->whereAny(['u.id','u.name','u.phone','u.national_id'],'LIKE','%'.$this->search.'%');
            })->when($this->unit , function (Builder $builder){
                $builder->where("user_roles.unit_id" , $this->unit);
            })
            ->when($this->item , function (Builder $builder){
                $builder->where("user_roles.item_id" , $this->item);
            })
            ->when($this->region , function (Builder $builder) {
                $builder->where(function (Builder $builder) {
                    $builder->where('region_id' , $this->region)
                        ->orWhereHas('region' , function (Builder $builder) {
                            $builder->where('id' , $this->region);
                        })->orWhereHas('unit' , function (Builder $builder) {
                            $builder->where('region_id' , $this->region);
                        });
                });
            })->when($this->role , function (Builder $builder) {
                $builder->where('user_roles.role' , $this->role);
            });
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function headings(): array
    {
        return [
            'نام',
            'کد ملی',
            'شماره تماس',
            'نقش',
            'منطقه',
            'محله',
            'نوع واحد حقوقی',
            'واحد حقوقی',
            'مرکز محوری بالادستی',
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            return [
                'name' => $row->name,
                'national_id' => $row->national_id,
                'phone' => $row->phone,
                'role' => $row->role->label(),
                'region' => $row->region?->title ?? $row->unit?->region?->title ?? 'none',
                'neighborhood' => $row->neighborhood?->title ?? $row->unit?->neighborhood?->title ?? 'none',
                'item' => $row->unit?->type?->label() ?? 'none',
                'unit' => $row->unit?->full ?? 'none',
                'parent_unit' => $row->unit?->parent?->full ?? 'none',
            ];
        });
    }
}
