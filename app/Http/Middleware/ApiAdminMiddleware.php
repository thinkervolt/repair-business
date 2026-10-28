<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;

class ApiAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => Lang::get('repair-business.error_admin-only-access'),
            ], 403);
        }

        return $next($request);
    }
}