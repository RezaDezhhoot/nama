<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodFeedController extends Controller
{
    public function __invoke(Request $request , $region = null)
    {
        $items = Neighborhood::query()->when($region , function ($q) use ($region) {
            $q->where('region_id' , $region);
        })->search($request->get('search'))->take(15)->select2()->get();
        return response()->json($items);
    }
}
