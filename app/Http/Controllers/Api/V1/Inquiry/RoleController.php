<?php

namespace App\Http\Controllers\Api\V1\Inquiry;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function show($code): UserResource
    {
        $user = User::query()
            ->with(['roles2','roles2.unit','roles2.city','roles2.region','roles2.neighborhood','roles2.area','roles2.unit.area','roles2.item','roles2.causer','roles2.editor'])
            ->where('national_id' , $code)->firstOrFail();

        return UserResource::make($user);
    }
}
