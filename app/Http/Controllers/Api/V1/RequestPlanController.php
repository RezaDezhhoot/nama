<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RequestPlanResource;
use App\Models\RequestPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RequestPlanController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return RequestPlanResource::collection(
            RequestPlan::query()->orderByDesc('bold')->when($request->has('soon') , function (Builder $builder) {
                $builder->comingSoon();
            })->when(! $request->has('soon') , function (Builder $builder) {
                $builder->published();
            })->paginate((int)$request->get('per_page' , 10))
        );
    }

    public function show($id): RequestPlanResource
    {
        return RequestPlanResource::make(
            RequestPlan::query()->withCount(['requests' => function ($q) {
                return $q->where('user_id' , auth()->id());
            }])->published()->findOrFail($id)
        );
    }
}
