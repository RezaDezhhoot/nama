<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;

class UserController extends Controller
{
    public function __invoke(): UserResource
    {
        $requests = RequestModel::query()->where('user_id' , auth()->id())->get();
        $reports = Report::query()->whereHas('request' , function (Builder $builder) {
            $builder->where('user_id' , auth()->id());
        })->get();

        return UserResource::make(auth()->user())->additional([
            'requests' => [
                RequestStatus::IN_PROGRESS->value => $requests->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $requests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $requests->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::DONE->value => $requests->where('status' , RequestStatus::REJECTED)->count(),
            ],
            'reports' => [
                RequestStatus::IN_PROGRESS->value => $reports->where('status' , RequestStatus::IN_PROGRESS)->count(),
                RequestStatus::REJECTED->value => $reports->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::ACTION_NEEDED->value => $reports->where('status' , RequestStatus::REJECTED)->count(),
                RequestStatus::DONE->value => $reports->where('status' , RequestStatus::REJECTED)->count(),
            ],
        ]);
    }
}
