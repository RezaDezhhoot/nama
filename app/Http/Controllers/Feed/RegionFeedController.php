<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionFeedController extends Controller
{
    public function __invoke(Request $request , $city = null)
    {
        $items = Region::query()->when($city , function ($q) use ($city) {
            $q->where('city_id' , $city);
        })->search($request->get('search'))->take(15)->select2()->get();
        return response()->json($items);
    }
}
