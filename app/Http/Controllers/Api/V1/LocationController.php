<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function regions(Request $request): JsonResponse
    {
        $items = Region::query()
            ->when($request->filled('q') , function ($q) use ($request) {
                $q->search($request->get('q'));
            })->paginate($request->get('per_page' , 10));
        return response()->json($items);
    }
}
