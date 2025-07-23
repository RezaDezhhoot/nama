<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFeedController extends Controller
{
    public function __invoke(Request $request , $withRoles = false)
    {
        $items = User::query()->when($withRoles , function (Builder $builder) {
            $db = config('database.connections.mysql.database');
            $builder->join(sprintf("%s.user_roles AS  ur",$db),"user_id",'=','users.id')->groupBy("users.id")->select("users.*");
        })->search($request->get('search'))->take(100)->select2()->get();
        return response()->json($items);
    }
}
