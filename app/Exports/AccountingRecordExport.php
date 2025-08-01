<?php

namespace App\Exports;

use App\Models\AccountingBatch;
use App\Models\AccountingRecord;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountingRecordExport implements FromQuery , WithHeadings,WithHeadingRow,ShouldAutoSize
{
    use Exportable;

    public function __construct(public AccountingBatch $batch)
    {
        ini_set('max_execution_time', '800');
        ini_set('memory_limit', '-1');
    }
    public function query()
    {
        return AccountingRecord::query()
            ->with(['batch','unit','region'])
            ->whereHas('batch')
            ->where('accounting_batch_id' , $this->batch->id)
            ->orderByDesc('id');
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function headings(): array
    {
        $headings = [
            'شناسه',
            'دسته',
            'شبا',
            'واحد حقوفی',
            'منطقه',
            'توضیحات',
            'تاریخ',
        ];
        foreach ($this->batch->plans['records'] as $r) {
            $headings[] = $r['plan'];
            $headings[] =  sprintf("%d %s",$r['totalFinalAmount'],'مبلغ کل');
            $headings[] = sprintf("%d %s",$r['count'],"برنامه");
            $headings[] = sprintf("%d %s",$r['students'],"نفرات");
        }
        return [
           ... $headings,
            'تعداد درخواست و گزارش',
            'نفرات'
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($row) {
            $records = [];
            foreach ($row->records['records'] as $r) {
                $records[] = $r['plan'];
                $records[] = (string)$r['totalFinalAmount'];
                $records[] = (string)$r['count'];
                $records[] = (string)$r['students'];
            }
            return [
                'id' => $row->id,
                'accounting_batch_id' => $row->accounting_batch_id,
                'sheba' => $row->sheba ?? '-',
                'unit' => $row->unit?->title ?? '-',
                'region' => $row->region?->title ?? '-',
                'type' => $row->type->label(),
                'created_at' => persian_date($row->created_at, "%A, %d %B %Y H:i:s"),
                ... $records,
                'requests_and_reports' => (string)$row->requests_and_reports,
                'students' => (string)$row->students,
            ];
        });
    }

}
