<?php

namespace App\Exports;
use App\Enums\FormItemType;
use App\Models\FormReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExportFormReports implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;
    public function __construct(public $user , public $form, public $status , public $search)
    {
    }

    public function query()
    {
        return FormReport::query()
            ->with(['form','user'])
            ->when($this->search , function ($q) {
                $q->search($this->search);
            })
            ->when($this->status , function ($q) {
                $q->where('status' , $this->status);
            })->when($this->form , function ($q) {
                $q->where('form_id' , $this->form);
            })->when($this->user , function ($q) {
                $q->where("user_id" , $this->user);
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
            'وضعیت',
            'فرم',
            'تاریخ ثبت',
            'تاریخ آخرین بروزرسنانی',
            'مربی',
        ];
    }
    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            $reports = [];
            foreach ($row->reports as $r) {
                if (is_array($r['value'])) {
                    $r['value'] = json_encode($r['value'],JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE);
                }
                if ($r['form']['type'] == FormItemType::FILE->value) {
                    $r['value'] = asset($r['value']);
                }

                $reports[] = $r['form']['title'].': '.$r['value'];
            }
            return [
                'id' => $row->id,
                'status' => $row->status?->label(),
                'form' => $row->form?->title,
                'created_at' => persian_date($row->created_at),
                'updated_at' => persian_date($row->updated_at),
                'user' => sprintf("%s - %s",$row->user?->name,$row->user?->national_id),
                ... $reports
            ];
        });
    }
}
