<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaFeedController extends Controller
{
    public function __invoke(Request $request , $neighborhood = null)
    {
        $items = Area::query()->when($neighborhood , function ($q) use ($neighborhood) {
            $q->where('neighborhood_id' , $neighborhood);
        })->search($request->get('search'))->take(15)->select2()->get();
        return response()->json($items);
    }
}
