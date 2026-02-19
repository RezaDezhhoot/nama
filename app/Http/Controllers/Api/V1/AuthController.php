<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SendAuthRequest;
use App\Services\Arman\ArmanOAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function sendRequest(SendAuthRequest $request): JsonResponse
    {
        try {
            $redirect = ArmanOAuth::make()->sendRequest(route('api.auth.verify'),[
                'callback' => $request->input('callback'),
            ]);
            if (! $redirect)
                throw new \Exception("invalid data");

            return response()->json([
                'verify_url' => $redirect
            ],201);
        } catch (\Exception $exception) {
            report($exception);
        }
        return response()->json([
            'message' =>  __('auth.unauthenticated')
        ],201);
    }

    public function verify(Request $request): RedirectResponse
    {
        $status = 401;
        $jwt = null;
        dd($request->all());
        $callback = $request->get('callback_data')['callback'] ?? "/";
        try {
            $user = ArmanOAuth::make()->verify($request->get('jwt',"-"));
            if ($user) {
                $status = 200;
                $jwt = $user->generateToken();
            }
        } catch (\Exception $exception) {
            report($exception);
        }
        $query = Arr::query([
            'status' => $status,
            'jwt' => $jwt,
        ]);
        return redirect()->away(sprintf("%s?%s",$callback , $query));
    }
}
