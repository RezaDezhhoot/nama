<?php

namespace App\Exports;

use App\Enums\OperatorRole;
use App\Models\Ring;
use App\Models\User;
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

    public function __construct(public $role = null , public $region = null  , public $unit = null , public $search  = null)
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
        return User::query()
            ->with(['roles','roles.unit','roles.region'])
            ->whereNotNull('name')
            ->leftJoin(sprintf("%s.user_roles AS  ur", $db),"user_id",'=','users.id')
            ->leftJoin(sprintf("%s.units AS u",$db),'u.id','=','ur.unit_id')
            ->select('ur.role as role2','ur.region_id','ur.unit_id','u.id AS unit_pkey','u.region_id AS unit_region_id','users.*')
            ->when($this->role , function (Builder $builder) {
                switch ($this->role) {
                    case OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES->value:
                    case OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING->value:
                    case OperatorRole::MOSQUE_HEAD_COACH->value:
                        $builder->where('ur.role' , $this->role);
                        break;
                    case OperatorRole::AREA_INTERFACE->value:
                    case OperatorRole::MOSQUE_CULTURAL_OFFICER->value:
                        $builder->where(function (Builder $builder) {
                            if ($this->region) {
                                $builder
                                    ->where(function (Builder $builder) {
                                        $builder
                                            ->where('ur.region_id' , $this->region)
                                            ->orWhere('u.region_id' , $this->region);
                                    });
                            } else {
                                $builder
                                    ->where('ur.role' , $this->role);
                            }
                        });
                        break;
                };
            })
            ->when($this->region && ! $this->role , function (Builder $builder){
                $builder->where(function (Builder $builder) {
                    $builder->where("ur.region_id" , $this->region)
                        ->orWhere('u.region_id' , $this->region);
                    ;
                });
            })
            ->when($this->unit , function (Builder $builder){
                $builder->where("ur.unit_id" , $this->unit);
            })
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->groupBy("users.id");
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
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            $roles = [];
            foreach ($row->roles as $role) {
                $roles[] = $role->role?->label().'-'.$role->unit?->full.'-'.$role->region?->title;
            }
            return [
                'name' => $row->name,
                'national_id' => $row->national_id,
                'phone' => $row->phone,
                ... $roles,
            ];
        });
    }
}
