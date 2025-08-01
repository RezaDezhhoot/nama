<?php

namespace App\Console\Commands;

use App\Data\AccountingPlanRecord;
use App\Data\AccountingPlanRecords;
use App\Enums\AccountingType;
use App\Enums\RequestStatus;
use App\Enums\UnitSubType;
use App\Enums\UnitType;
use App\Models\AccountingBatch;
use App\Models\AccountingRecord;
use App\Models\RequestPlan;
use App\Models\Unit;
use DeepCopy\DeepCopy;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid;

class PrepareDailyAccountingRecordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prepare-daily-accounting-record-command {type} {--subType=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unitType = UnitType::tryFrom($this->argument('type'));
        if (! $unitType) {
            return;
        }
        $unitSubType = UnitSubType::tryFrom($this->option('subType'));

        $records = [];
        $units = Unit::query()
            ->orderBy('id')
            ->with(['region','requests' => function ($q) {
                $q->with(['plan'])->where('status' , RequestStatus::DONE->value);
            },'requests.report' => function ($q) {
                $q->where('status' , RequestStatus::DONE->value);
            }])
            ->where('type' , $unitType)->when($unitSubType , function (Builder $builder) use ($unitSubType) {
                $builder->where('sub_type' , $unitSubType);
            })->get();

        $plans = RequestPlan::query()->orderBy('id')
            ->whereHas('item' , function (Builder $builder) use ($unitType) {
                $builder->where('type' , $unitType);
            })->withTrashed()->cursor()->pluck('title','id');
        $subRecords = new AccountingPlanRecords();
        $plans->each(function ($v , $i) use (&$subRecords) {
            $subRecords->add(new AccountingPlanRecord($v , $i));
        });
        $now = now()->format("Y-m-d H:i:s");

        $batch = new AccountingBatch;
        $batch->unit_type = $unitType;
        $batch->unit_sub_type = $unitSubType;
        $batch->id = Uuid::uuid4();
        $batch->batch = (AccountingBatch::query()->orderByDesc('batch')->where('unit_type' , $unitType)->when($unitSubType , function ($q) use ($unitSubType) {
            $q->where('unit_sub_type',$unitSubType);
        })->first()?->batch ?? 0) + 1;

        $copier = new DeepCopy;
        $totalSubRecords = $copier->copy($subRecords);
        foreach ($units as $unit) {
            $reqSubRecords = $copier->copy($subRecords);
            $repSubRecords = $copier->copy($subRecords);

            $requestsAndReports = 0;
            $students = 0;
            $requestsAndReports2 = 0;
            $students2 = 0;

            $reqRecord = [
                'sheba' => ! empty($unit->number_list) ? implode(',',$unit->number_list) : null,
                'unit_id' => $unit->id,
                'region_id' => $unit->region_id,
                'type' => AccountingType::REQUEST->value,
                'unit_type' => $unitType->value,
                'unit_sub_type' => $unitSubType->value,
                'created_at' => $now,
                'updated_at' => $now,
                'accounting_batch_id' => $batch->id,
            ];
            $repRecord = [
                ... $reqRecord,
                'type' => AccountingType::REPORT->value,
            ];
            foreach ($unit->requests as $request) {
                $planData = ! empty($request->plan_data) ? $request->plan_data : $request->plan?->toArray();
                if (empty($planData)) continue;

                $rsrReq = $reqSubRecords->find($request->request_plan_id);
                if (! $rsrReq) {
                    $rsrReq = $reqSubRecords->add(new AccountingPlanRecord($planData['title'] ?? 'u',$request->request_plan_id));
                }
                $tSubRecords = $totalSubRecords->find($request->request_plan_id);
                if (! $tSubRecords) {
                    $tSubRecords = $totalSubRecords->add(new AccountingPlanRecord($planData['title'] ?? 'u',$request->request_plan_id));
                }

                $rsrRep = $repSubRecords->find($request->request_plan_id);
                if (! $rsrRep)
                    $rsrRep = $repSubRecords->add(new AccountingPlanRecord($planData['title'] ?? 'u',$request->request_plan_id));

                $rsrReq->addCount()->addIds($request->id)
                    ->addStudents($request->students)
                    ->addTotalFinalAmount($request->final_amount);

                $tSubRecords->addCount()->addIds($request->id)
                    ->addStudents($request->students)
                    ->addTotalFinalAmount($request->final_amount);

                $requestsAndReports++;
                $students += $rsrReq->getStudents();

                if ($request->report) {
                    $rsrRep->addCount()->addIds($request->report->id)
                        ->addStudents($request->report->students)
                        ->addTotalFinalAmount($request->report->final_amount);

                    $tSubRecords->addCount()->addIds($request->report->id)
                        ->addStudents($request->report->students)
                        ->addTotalFinalAmount($request->report->final_amount);

                    $requestsAndReports++;

                    $requestsAndReports2++;
                    $students2 += $rsrRep->getStudents();
                }
            }
            // Request
            $reqRecord['requests_and_reports']  = $requestsAndReports;
            $reqRecord['students']              = $students;
            $reqRecord['records']               = $reqSubRecords->toJson();
            // Report
            $repRecord['requests_and_reports']  = $requestsAndReports2;
            $repRecord['students']              = $students2;
            $repRecord['records']               = $repSubRecords->toJson();
            // Add
            $records[] = $reqRecord;
            $records[] = $repRecord;
        }
        $batch->fill([
            'plans' => $totalSubRecords->toArray()
        ])->save();
        AccountingRecord::query()->insert($records);
    }
}
