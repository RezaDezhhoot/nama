<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubmitClientLogRequest;
use App\Http\Resources\Api\V1\ClientLogResource;
use App\Models\ClientLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientLogController extends Controller
{
    public function __invoke(SubmitClientLogRequest $request): ClientLogResource|JsonResponse
    {
        $data = $request->validated();
        $data['headers'] = $request->header();
        $data['agent'] = $request->userAgent();
        $data['ip'] = $request->ip();
        try {
            $log = new ClientLog;
            if (auth('sanctum')->check()) {
                $log->user()->associate(auth('sanctum')->user());
            }
            $log->fill($data);
            $log->save();
            return ClientLogResource::make($log);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'error' => 'server error'
            ] , 500);
        }
    }
}
