<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitFeedController extends Controller
{
    public function __invoke(Request $request , bool $parent = true)
    {
        $items = Unit::query()->when($parent , function ($q) {
            $q->whereNull('parent_id');
        })->search($request->get('search'))->take(200)->select2()->get();
        return response()->json($items);
    }
}
