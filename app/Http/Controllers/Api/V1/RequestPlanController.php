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
        $q = RequestPlan::query()->when($request->filled('q') , function ($q) use($request) {
            $q->search($request->get('q'));
        })->where('item_id',\request()->get('item_id'))->orderByDesc('bold')->where(function (Builder $builder) {
            $builder->where('starts_at' ,'<=' ,now())->orWhereNull('starts_at');
        })->where(function (Builder $builder) {
            $builder->where('expires_at' ,'>=' ,now())->orWhereNull('expires_at');
        })->when($request->has('soon') , function (Builder $builder) {
            $builder->comingSoon();
        })->when(! $request->has('soon') , function (Builder $builder) {
            $builder->published();
        });
        if (! $request->query('ignore_requirements')) {
            $q->with('requirementsv')->where(function (Builder $builder) {
                $builder->where('golden' , false)->orWhere(function (Builder $builder) {
                    $builder->where('golden' , true)->whereHas('limits' , function (Builder $builder) {
                        $builder->where('value' , auth()->user()->national_id);
                    });
                });
            });
        } else {
            $q->withoutGlobalScopes()->select(['id','title','expires_at','item_id','starts_at','expires_at','status']);
        }
        $q = $q->paginate((int)$request->get('per_page' , 10));

        return RequestPlanResource::collection($q);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        $q = RequestPlan::query()->when($request->filled('q') , function ($q) use($request) {
            $q->search($request->get('q'));
        })->where('item_id',\request()->get('item_id'))->orderByDesc('bold')
            ->whereHas('requests');

        if (! $request->query('ignore_requirements')) {
            $q->with('requirementsv');
        } else {
            $q->withoutGlobalScopes()->select(['id','title','expires_at','item_id','starts_at','expires_at']);
        }
        $q = $q->paginate((int)$request->get('per_page' , 10));
        return RequestPlanResource::collection($q);
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
