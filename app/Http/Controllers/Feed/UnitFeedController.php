<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitFeedController extends Controller
{
    public function __invoke(Request $request , bool $parent = true , $type = null , $main_unit = null): JsonResponse
    {
        $items = Unit::query()->when($parent , function ($q) {
            $q->whereNull('parent_id');
        })->when($type , function ($q) use ($type) {
            $q->where('type',$type);
        })->when($main_unit , function ($q) use ($main_unit) {
            $q->where('parent_id',$main_unit);
        })->search($request->get('search'))->take(200)->select2()->get();
        return response()->json($items);
    }
}
