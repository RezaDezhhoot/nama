<?php

namespace App\Http\Middleware;

use App\Models\DashboardItem;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('item_id') && DashboardItem::query()->where('id',$request->item_id)->exists()) {
            return $next($request);
        }

        abort(403);
    }
}
