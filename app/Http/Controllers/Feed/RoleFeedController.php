<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleFeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $items = Role::query()->whereNotIn('name',['super_admin','administrator'])->search($request->get('search'))->take(200)->select2()->get();
        return response()->json($items);
    }
}
