<?php

namespace App\Http\Controllers\Api\V1\Inquiry;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __invoke(Request $request)
    {
        if (app()->environment('production') && $request->header('X-API-KEY') !== config('services.pusheh.api_key')) {
            return response()->json([
                'message' => "unauthorized",
            ] , 401);
        }
        $items = [];
        $db = config('database.connections.arman.database');
        \App\Models\Request::query()
            ->without(['item','user','unit','members','members.image','plan'])
            ->with(['imamLetter','areaInterfaceLetter','images','otherImamLetter','otherAreaInterfaceLetter'])
            ->where('step',RequestStep::FINISH)
            ->join('request_plans AS rp','rp.id','=','requests.request_plan_id')
            ->join($db.'.users AS u','u.id','=','requests.user_id')
            ->select(['requests.*','rp.title as plan_title','u.national_id AS user_national_id'])
            ->chunk(300 , function ($reqs) use(&$items) {
                $items[] = $reqs;
            });
        return response()->json($items);
    }
}
