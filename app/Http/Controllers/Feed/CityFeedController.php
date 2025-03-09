<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityFeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $items = City::query()->search($request->get('search'))->take(15)->select2()->get();
        return response()->json($items);
    }
}
