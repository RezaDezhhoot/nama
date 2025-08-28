<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Report;
use App\Models\WrittenRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;

class UserController extends Controller
{
    public function __invoke(): UserResource
    {
        auth()->user()->load(['roles2' => function ($q) {
            if (\request()->filled('item_id')) {
                return $q->where('item_id' , \request()->item_id);
            }
            return $q;
        },'roles2.item']);


        return UserResource::make(auth()->user());
    }
}
