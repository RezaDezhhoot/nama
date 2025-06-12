<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\RequestPlan;
use App\Models\Unit;
use Illuminate\Http\Request;

class PlanFeedController extends Controller
{
    public function __invoke(Request $request ,  $type = null , $ignore = null)
    {
        $items = RequestPlan::query()->when($type , function ($q) use($type) {
            $q->whereHas('item' , function ($q) use($type){
                $q->where('type' , $type);
            });
        })->when($ignore , function ($q) use ($ignore) {
            $q->where('id','!=' , $ignore);
        })->search($request->get('search'))->take(100)->select2()->get();

        return response()->json($items);
    }
}
