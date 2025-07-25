<?php

namespace App\Exports;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportReports implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;
    public function __construct(public $type , public $step , public $plan , public $unit , public $region , public $status , public $search)
    {
    }

    public function query()
    {
        return Report::query()
            ->with(['request','request.user','request.unit','request.unit.city','request.unit.region','request.plan','video','images'])
            ->when($this->step , function (Builder $builder) {
                $builder->where('step' , $this->step);
            })
            ->withCount('comments')
            ->when($this->region , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('unit' , function (Builder $builder) {
                        $builder->where('region_id' , $this->region);
                    });
                });
            })
            ->when($this->unit , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('unit' , function (Builder $builder) {
                        $builder->where('id' , $this->unit);
                    });
                });
            })
            ->when($this->plan , function (Builder   $builder) {
                $builder->whereHas('request' , function (Builder $builder) {
                    $builder->whereHas('plan' , function (Builder $builder){
                        $builder->where('id',$this->plan);
                    });
                });
            })
            ->latest('updated_at')
            ->when($this->type , function ($q) {
                $q->whereHas('item' , function ($q){
                    $q->where('type' , $this->type);
                });
            })
            ->whereHas('request' , function (Builder $builder) {
                $builder->when($this->search , function (Builder $builder) {
                    $builder->search($this->search )->orWhereHas('plan' , function (Builder $builder) {
                        $builder->search($this->search);
                    })->orWhere(function (Builder $builder) {
                        $builder->whereIn('user_id' , User::query()->search($this->search )->take(30)->get()->pluck('id')->toArray());
                    })->orWhereHas('unit' , function (Builder $builder)  {
                        $builder->search($this->search );
                    });
                });
            })
            ->confirmed()
            ->roleFilter()
            ->when($this->status , function (Builder $builder) {
                $builder->where('status' , $this->status);
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
            'تاریخ ارسال',
            'تاریخ اخرین بروزرسانی',
            'هزینه پیشنهادی مرحله دوم توسط معاونت اجرایی مساجد',
            'هزینه نهایی تایید شده مرحله دوم توسط معاونت طرح و برنامه',
            'تعداد دانش آموزان نوجوان',
            'تاریخ برگزاری',
            'تصاویر',
            'ویدیو',
        ];
    }
    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            return [
                'id' => $row->id,
                'plan' => $row->request?->plan?->title,
                'single_step' => $row->request?->single_step ? 'بله' : 'خیر',
                'staff' => $row->request?->staff ? 'بله' : 'خیر',
                'golden' => $row->request?->golden ? 'بله' : 'خیر',
                'name' => $row->request?->user?->name,
                'phone' => $row->request?->user?->phone,
                'national_id' => $row->request?->user->national_id,
                'status' => $row->status->label(),
                'step' => $row->step->label(),
                'unit' => sprintf("%s - %s",$row?->request?->unit?->title , $row->request?->unit?->text),
                'city' => $row->request->unit?->city?->title,
                'region' => $row->request->unit?->region?->title,
                'created_at' =>  persian_date($row->created_at),
                'updated_at' =>  persian_date($row->updated_at),
                'offer_amount' => number_format($row->offer_amount),
                'final_amount' => number_format($row->final_amount),
                'students' => $row->students,
                'date' => persian_date($row->date),
                'images' => $row->images?->pluck('url')?->implode(','),
                'video' => $row->video?->url,
            ];
        });
    }
}
