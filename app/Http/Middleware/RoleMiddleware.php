<?php

namespace App\Http\Middleware;

use App\Models\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('role') && UserRole::query()->where([
            ['user_id' , auth()->id()],
            ['role' ,  $request->get('role')],
            ['item_id' ,  $request->get('item_id')],
            ])->exists()) {
            return $next($request);
        }
        abort(403);
    }
}
