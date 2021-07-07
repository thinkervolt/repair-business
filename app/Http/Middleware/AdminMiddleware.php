<?php

namespace App\Http\Middleware;

use Closure;

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
            return redirect()->route('dashboard')->with('error','You need to be Administrator to perform this Action.')->with('alert', 'alert-danger');
        }


        return $next($request);
    }
}
