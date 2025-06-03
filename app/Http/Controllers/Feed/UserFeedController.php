<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserFeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $items = User::query()->search($request->get('search'))->take(100)->select2()->get();
        return response()->json($items);
    }
}
