<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Support\Facades\Lang;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role !== "admin") {
            return redirect()->route('dashboard')->with('error',Lang::get('repair-business.error_admin-only-access')  )->with('alert', 'alert-danger');
        }


        return $next($request);
    }
}
