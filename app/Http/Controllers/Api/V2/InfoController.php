<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Request as RequestModel;
use App\Models\RequestPlan;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    private function newReqQ($withStep = false)
    {
        $role = OperatorRole::from(request()->get('role'));
        $steps = "'" . implode("','", $role->stepArr()) . "'";
        $next =  "'" . implode("','", $role->nextArr()) . "'";

        return RequestModel::query()
            ->selectRaw(
                sprintf("
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN step IN ($next)
                    THEN 1 ELSE 0
                END) AS %s
            " , RequestStatus::IN_PROGRESS->value ,  RequestStatus::REJECTED->value , RequestStatus::ACTION_NEEDED->value , RequestStatus::DONE->value , RequestStatus::DONE->value."_temp"
            ) , [
                RequestStatus::IN_PROGRESS->value,
                RequestStatus::REJECTED->value,
                RequestStatus::ACTION_NEEDED->value,
                RequestStatus::DONE->value,
            ])
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->limit(1)
            ->first()
            ;
    }

    private function newRepQ($withStep = false)
    {
        $role = OperatorRole::from(request()->get('role'));
        $steps = "'" . implode("','", $role->stepArr()) . "'";
        $next =  "'" . implode("','", $role->nextArr()) . "'";

        return Report::query()
            ->selectRaw(
                sprintf("
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    ".($withStep ? "AND step IN ($steps)" : '')."
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN step IN ($next)
                    THEN 1 ELSE 0
                END) AS %s
            " , RequestStatus::IN_PROGRESS->value ,  RequestStatus::REJECTED->value , RequestStatus::ACTION_NEEDED->value , RequestStatus::DONE->value,RequestStatus::PENDING->value , RequestStatus::DONE->value."_temp"
                ) , [
                RequestStatus::IN_PROGRESS->value,
                RequestStatus::REJECTED->value,
                RequestStatus::ACTION_NEEDED->value,
                RequestStatus::DONE->value,
                RequestStatus::PENDING->value,
            ])
            ->item(\request()->get('item_id'))
            ->role(\request()->get('role'))
            ->limit(1)
            ->first()
            ;
    }

    private function newWReqQ()
    {
        return WrittenRequest::query()
            ->where('user_id' , auth()->id())
            ->selectRaw(
                sprintf("
                SUM(CASE
                    WHEN status = ?
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    THEN 1 ELSE 0
                END) AS %s,
                SUM(CASE
                    WHEN status = ?
                    THEN 1 ELSE 0
                END) AS %s
            " , RequestStatus::IN_PROGRESS->value ,  RequestStatus::REJECTED->value , RequestStatus::ACTION_NEEDED->value , RequestStatus::DONE->value
                ) , [
                RequestStatus::IN_PROGRESS->value,
                RequestStatus::REJECTED->value,
                RequestStatus::ACTION_NEEDED->value,
                RequestStatus::DONE->value,
            ])
                ->limit(1)
                ->first()
            ;
    }

    public function __invoke(): JsonResponse
    {
        $role = OperatorRole::from(request()->get('role'));
        $isNotCoach = $role !== OperatorRole::MOSQUE_HEAD_COACH;
        if ($isNotCoach) {
            $requestData = $this->newReqQ(true);
            $reportData = $this->newRepQ(true);
            $requestsRes = [
                RequestStatus::IN_PROGRESS->value => $requestData->{RequestStatus::IN_PROGRESS->value},
                RequestStatus::REJECTED->value => $requestData->{RequestStatus::REJECTED->value},
                RequestStatus::ACTION_NEEDED->value => $requestData->{RequestStatus::ACTION_NEEDED->value},
                RequestStatus::DONE->value => $requestData->{RequestStatus::DONE->value},
                RequestStatus::DONE->value.'_temp' => $requestData->{RequestStatus::DONE->value.'_temp'},
            ];
            $reportsRes = [
                RequestStatus::IN_PROGRESS->value => $reportData->{RequestStatus::IN_PROGRESS->value},
                RequestStatus::REJECTED->value => $reportData->{RequestStatus::REJECTED->value},
                RequestStatus::ACTION_NEEDED->value => $reportData->{RequestStatus::ACTION_NEEDED->value},
                RequestStatus::DONE->value => $reportData->{RequestStatus::DONE->value},
                RequestStatus::PENDING->value => $reportData->{RequestStatus::PENDING->value},
                RequestStatus::DONE->value.'_temp' => $reportData->{RequestStatus::DONE->value.'_temp'},
            ];
        } else {
            $requestData = $this->newReqQ();
            $reportData = $this->newRepQ();
            $requestsRes = [
                RequestStatus::IN_PROGRESS->value => $requestData->{RequestStatus::IN_PROGRESS->value},
                RequestStatus::REJECTED->value => $requestData->{RequestStatus::REJECTED->value},
                RequestStatus::ACTION_NEEDED->value => $requestData->{RequestStatus::ACTION_NEEDED->value},
                RequestStatus::DONE->value => $requestData->{RequestStatus::DONE->value},
            ];
            $reportsRes = [
                RequestStatus::IN_PROGRESS->value => $reportData->{RequestStatus::IN_PROGRESS->value},
                RequestStatus::REJECTED->value => $reportData->{RequestStatus::REJECTED->value},
                RequestStatus::ACTION_NEEDED->value => $reportData->{RequestStatus::ACTION_NEEDED->value},
                RequestStatus::DONE->value => $reportData->{RequestStatus::DONE->value},
                RequestStatus::PENDING->value => $reportData->{RequestStatus::PENDING->value},
            ];
        }
        $wrequestData = $this->newWReqQ();

        return response()->json([
            'requests' => $requestsRes,
            'reports' => $reportsRes,
            'written-requests' => [
                RequestStatus::IN_PROGRESS->value => $wrequestData->{RequestStatus::IN_PROGRESS->value},
                RequestStatus::REJECTED->value => $wrequestData->{RequestStatus::REJECTED->value},
                RequestStatus::ACTION_NEEDED->value => $wrequestData->{RequestStatus::ACTION_NEEDED->value},
                RequestStatus::DONE->value => $wrequestData->{RequestStatus::DONE->value},
            ],
            'plans' => RequestPlan::query()->where('item_id',\request()->get('item_id'))->published()->count()
        ]);
    }
}
