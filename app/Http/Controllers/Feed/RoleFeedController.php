<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleFeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $inValidRoles = ['super_admin','administrator'];
        if (auth()->user()->hasRole('administrator')) {
            $inValidRoles = ['administrator'];
        }
        $items = Role::query()->whereNotIn('name',$inValidRoles)->search($request->get('search'))->take(200)->select2()->get();
        return response()->json($items);
    }
}
