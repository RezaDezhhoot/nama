<?php

namespace App\Exports;
use App\Enums\UnitType;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportRequests implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;
    public function __construct(public $type , public $step , public $plan , public $unit , public $region , public $status , public $search , public $version)
    {
        ini_set('max_execution_time', '-1');
        ini_set('memory_limit', '-1');
    }

    public function query()
    {
        return Request::query()
            ->with(['plan','user','unit','unit.city','unit.region','imamLetter','areaInterfaceLetter'])
            ->when($this->step , function (Builder $builder) {
                $builder->where('step' , $this->step);
            })
            ->withCount('comments')
            ->when($this->plan , function (Builder $builder){
                $builder->whereHas('plan' , function (Builder $builder){
                    $builder->where('id',$this->plan);
                });
            })
            ->when($this->unit , function (Builder $builder){
                $builder->where('unit_id', $this->unit);
            })
            ->when($this->region , function (Builder $builder) {
                $builder->whereHas('unit' , function (Builder $builder) {
                    $builder->where('region_id' , $this->region);
                });
            })
            ->latest('updated_at')
            ->when($this->type , function ($q) {
                $q->whereHas('item' , function ($q){
                    $q->where('type' , $this->type);
                });
            })
            ->when($this->version , function (Builder $builder) {
                $builder->whereHas('plan' , function (Builder $builder) {
                    $builder->where('version' , $this->version);
                });
            })
            ->whereHas('plan')
            ->confirmed()
            ->when($this->status , function (Builder $builder) {
                $builder->where('status' , $this->status);
            })->when($this->search , function (Builder $builder) {
                $builder->search($this->search )->orWhereHas('plan' , function (Builder $builder) {
                    $builder->search($this->search);
                })->orWhere(function (Builder $builder) {
                    $builder->whereIn('user_id' , User::query()->search($this->search )->take(30)->get()->pluck('id')->toArray());
                })->orWhereHas('unit' , function (Builder $builder)  {
                    $builder->search($this->search );
                });
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
            'پلن',
            'درخواست تک مرحله ای',
            'درخواست ستادی',
            'درخواست طلایی',
            'نام مربی',
            'شماره مربی',
            'کد ملی مربی',
            'وضعیت',
            'مرحله',
            'مرکز',
            'شهر',
            'منطقه',
            'هزینه پرداختی توسط آرمان(ثبت سیستمی)',
            'هزینه پیشنهادی توسط معاونت اجرایی',
            'هزینه نهایی تایید شده توسط معاونت طرح و برنامه',
            ...($this->type != UnitType::UNIVERSITY->value ? ['هزینه کلی عملیات','تعداد دانش آموزان نوجوان','فایل نامه رابط منطقه', 'فایل پیوست نامه امام جماعت'] : ['عنوان برنامه','محل برگزاری']),
            'تاریخ ارسال',
            'تاریخ اخرین بروزرسانی',
            'تاریخ برگزاری',
            'توضیحات تکمیلی',
        ];
    }
    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            return [
                'id' => $row->id,
                'plan' => $row?->plan?->title,
                'single_step' => $row->single_step ? 'بله' : 'خیر',
                'staff' => $row->staff ? 'بله' : 'خیر',
                'golden' => $row->golden ? 'بله' : 'خیر',
                'name' => $row?->user?->name,
                'phone' => $row?->user?->phone,
                'national_id' => $row?->user?->national_id,
                'status' => $row?->status->label(),
                'step' => $row->step->label($row->plan_type),
                'unit' => sprintf("%s - %s",$row?->unit?->title , $row?->unit?->text),
                'city' => $row->unit?->city?->title,
                'region' => $row->unit?->region?->title,
                'total_amount' =>  $row->designated_by_council ? "هزینه توسط شورا تعیین می گردد" : number_format($row->staff ? $row->staff_amount : $row->total_amount),
                'offer_amount' =>  number_format($row->offer_amount),
                'final_amount' =>  number_format($row->final_amount),
                ...($this->type != UnitType::UNIVERSITY->value ? ['amount' =>  number_format($row->amount) , 'students' => $row->students , 'areaInterfaceLetter' => $row->areaInterfaceLetter?->url , 'imamLetter' => $row->imamLetter?->url] :
                    ['title' => $row->title,'location' => $row->location]),
                'created_at' =>  persian_date($row->created_at),
                'updated_at' =>  persian_date($row->updated_at),
                'date' => persian_date($row->date),
                'body' => $row->body,
            ];
        });
    }
}
