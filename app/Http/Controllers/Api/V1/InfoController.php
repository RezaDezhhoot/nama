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
    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        $requests = RequestModel::query()->item(\request()->get('item_id'))->role(\request()->get('role'))->get();
        $reports = Report::query()->item(\request()->get('item_id'))->role(\request()->get('role'))->get();
        $role = OperatorRole::from(request()->get('role'));

        $writtenRequests = WrittenRequest::query()->where('user_id' , auth()->id())->get();
        $requestsRes = [
            RequestStatus::IN_PROGRESS->value => $requests->where('status' , RequestStatus::IN_PROGRESS)->whereIn('step',$role->step())->count(),
            RequestStatus::REJECTED->value => $requests->where('status' , RequestStatus::REJECTED)->whereIn('step',$role->step())->count(),
            RequestStatus::ACTION_NEEDED->value => $requests->where('status' , RequestStatus::ACTION_NEEDED)->whereIn('step',$role->step())->count(),
            RequestStatus::DONE->value => $requests->where('status' , RequestStatus::DONE)->whereIn('step',$role->step())->count(),
        ];
        $reportsRes = [
            RequestStatus::IN_PROGRESS->value => $reports->where('status' , RequestStatus::IN_PROGRESS)->whereIn('step',$role->step())->count(),
            RequestStatus::REJECTED->value => $reports->where('status' , RequestStatus::REJECTED)->whereIn('step',$role->step())->count(),
            RequestStatus::ACTION_NEEDED->value => $reports->where('status' , RequestStatus::ACTION_NEEDED)->whereIn('step',$role->step())->count(),
            RequestStatus::DONE->value => $reports->where('status' , RequestStatus::DONE)->whereIn('step',$role->step())->count(),
            RequestStatus::PENDING->value => $reports->where('status' , RequestStatus::PENDING)->whereIn('step',$role->step())->count(),
        ];
        if ($role !== OperatorRole::MOSQUE_HEAD_COACH) {
            $requestsRes[RequestStatus::DONE->value."_temp"] = $requests->whereIn('step',$role->next())->count();
            $reportsRes[RequestStatus::DONE->value."_temp"] = $reports->whereIn('step',$role->next())->count();
        }
        return response()->json([
            'requests' => $requestsRes,
            'reports' => $reportsRes,
            'written-requests' => [
                RequestStatus::IN_PROGRESS->value => $writtenRequests->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $writtenRequests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $writtenRequests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::DONE->value => $writtenRequests->where('status' , RequestStatus::DONE)->count(),
            ],
            'plans' => RequestPlan::query()->where('item_id',\request()->get('item_id'))->published()->count()
        ]);
    }
}
