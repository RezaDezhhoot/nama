<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Request as RequestModel;
use App\Models\RequestPlan;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    private function newReqQ()
    {
        return  RequestModel::query()->limit(100)->item(\request()->get('item_id'))->role(\request()->get('role'));
    }

    private function newRepQ()
    {
        return Report::query()->limit(100)->item(\request()->get('item_id'))->role(\request()->get('role'));
    }

    private function newWReqQ()
    {
        return WrittenRequest::query()->limit(100)->where('user_id' , auth()->id());
    }

    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        $role = OperatorRole::from(request()->get('role'));
        $isNotCoach = $role !== OperatorRole::MOSQUE_HEAD_COACH;
        if ($isNotCoach) {
            $requestsRes = [
                RequestStatus::IN_PROGRESS->value => $this->newReqQ()->where('status' , RequestStatus::IN_PROGRESS)->whereIn('step',$role->step())->count(),
                RequestStatus::REJECTED->value => $this->newReqQ()->where('status' , RequestStatus::REJECTED)->whereIn('step',$role->step())->count(),
                RequestStatus::ACTION_NEEDED->value => $this->newReqQ()->where('status' , RequestStatus::ACTION_NEEDED)->whereIn('step',$role->step())->count(),
                RequestStatus::DONE->value => $this->newReqQ()->where('status' , RequestStatus::DONE)->whereIn('step',$role->step())->count(),
            ];
            $reportsRes = [
                RequestStatus::IN_PROGRESS->value => $this->newRepQ()->where('status' , RequestStatus::IN_PROGRESS)->whereIn('step',$role->step())->count(),
                RequestStatus::REJECTED->value => $this->newRepQ()->where('status' , RequestStatus::REJECTED)->whereIn('step',$role->step())->count(),
                RequestStatus::ACTION_NEEDED->value => $this->newRepQ()->where('status' , RequestStatus::ACTION_NEEDED)->whereIn('step',$role->step())->count(),
                RequestStatus::DONE->value => $this->newRepQ()->where('status' , RequestStatus::DONE)->whereIn('step',$role->step())->count(),
                RequestStatus::PENDING->value => $this->newRepQ()->where('status' , RequestStatus::PENDING)->whereIn('step',$role->step())->count(),
            ];
            $requestsRes[RequestStatus::DONE->value."_temp"] = $this->newReqQ()->whereIn('step',$role->next())->count();
            $reportsRes[RequestStatus::DONE->value."_temp"] = $this->newRepQ()->whereIn('step',$role->next())->count();
        } else {
            $requestsRes = [
                RequestStatus::IN_PROGRESS->value => $this->newReqQ()->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $this->newReqQ()->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $this->newReqQ()->where('status' , RequestStatus::ACTION_NEEDED)->count(),
                RequestStatus::DONE->value => $this->newReqQ()->where('status' , RequestStatus::DONE)->count(),
            ];
            $reportsRes = [
                RequestStatus::IN_PROGRESS->value => $this->newRepQ()->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $this->newRepQ()->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $this->newRepQ()->where('status' , RequestStatus::ACTION_NEEDED)->count(),
                RequestStatus::DONE->value => $this->newRepQ()->where('status' , RequestStatus::DONE)->count(),
                RequestStatus::PENDING->value => $this->newRepQ()->where('status' , RequestStatus::PENDING)->count(),
            ];
        }

        return response()->json([
            'requests' => $requestsRes,
            'reports' => $reportsRes,
            'written-requests' => [
                RequestStatus::IN_PROGRESS->value => $this->newWReqQ()->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $this->newWReqQ()->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $this->newWReqQ()->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::DONE->value => $this->newWReqQ()->where('status' , RequestStatus::DONE)->count(),
            ],
            'plans' => RequestPlan::query()->where('item_id',\request()->get('item_id'))->published()->count()
        ]);
    }
}
