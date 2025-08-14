<?php

namespace App\Http\Controllers\Api\V1\Inquiry;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __invoke(Request $request)
    {
        if (app()->environment('production') && $request->header('X-API-KEY') !== config('services.pusheh.api_key')) {
            return response()->json([
                'message' => "unauthorized",
            ] , 401);
        }

        $units = [];
        Unit::query()
            ->with(['roles'])
            ->chunkById(200 , function ($items) use (&$units) {
                $units[] = $items;
            });

        return response()->json($units);
    }
}
