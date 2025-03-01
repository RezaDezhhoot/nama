<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Request as RequestModel;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        $requests = RequestModel::query()->item(\request()->get('item_id'))->role(\request()->get('role'))->get();
        $reports = Report::query()->item(\request()->get('item_id'))->role(\request()->get('role'))->get();
        $writtenRequests = WrittenRequest::query()->where('user_id' , auth()->id())->get();
        return response()->json([
            'requests' => [
                RequestStatus::IN_PROGRESS->value => $requests->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $requests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $requests->where('status' , RequestStatus::ACTION_NEEDED)->count(),
                RequestStatus::DONE->value => $requests->where('status' , RequestStatus::DONE)->count(),
            ],
            'reports' => [
                RequestStatus::IN_PROGRESS->value => $reports->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $reports->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $reports->where('status' , RequestStatus::ACTION_NEEDED)->count(),
                RequestStatus::DONE->value => $reports->where('status' , RequestStatus::DONE)->count(),
            ],
            'written-requests' => [
                RequestStatus::IN_PROGRESS->value => $writtenRequests->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $writtenRequests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $writtenRequests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::DONE->value => $writtenRequests->where('status' , RequestStatus::DONE)->count(),
            ],
        ]);
    }
}
