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
            ->when($request->filled('q') , function (Builder $builder) use ($request) {
                $builder->where(function (Builder $builder) use ($request) {
                    $builder->search($request->get('q'))
                        ->orWhereHas('region' , function ($q) use ($request) {
                            $q->search($request->get('q'));
                        });
                });
            })
            ->paginate($request->get('per_page' , 10));

        return UnitResource::collection($units);
    }
}
