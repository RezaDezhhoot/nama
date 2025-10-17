<?php

namespace App\Http\Controllers\Api\V1\Inquiry;

use App\Enums\RequestStep;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        if (app()->environment('production') && $request->header('X-API-KEY') !== config('services.pusheh.api_key')) {
            return response()->json([
                'message' => "unauthorized",
            ] , 401);
        }
        $items = Report::query()
            ->with(['images','images2','video','otherVideos'])
            ->where('reports.step',RequestStep::FINISH)
            ->join('requests AS r','r.id','=','reports.request_id')
            ->select(['reports.*','r.unit_id AS unit_id','r.user_id AS user_id'])
            ->paginate(100);

        return response()->json($items);
    }
}
