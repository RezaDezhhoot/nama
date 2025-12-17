<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackErrorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->status() >= 400) {
            if ($response instanceof JsonResponse) {
                $responseContent = $response->getData(true);
            } else {
                $responseContent = mb_convert_encoding(
                    $response->getContent(),
                    'UTF-8',
                    'UTF-8'
                );
            }

            Log::error('HTTP Error', [
                'status' => $response->status(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'response' => $responseContent,
            ]);
        }
        return $response;
    }
}
