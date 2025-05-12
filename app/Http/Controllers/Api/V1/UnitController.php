<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $units = Unit::query()
            ->whereHas('requests' , function (Builder $builder) use ($request) {
                $builder->item(\request()->get('item_id'))->role(\request()->get('role'));
            })->paginate($request->get('per_page' , 10));

        return UnitResource::collection($units);
    }
}
