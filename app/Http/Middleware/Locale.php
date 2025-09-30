<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class Locale
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

        $locale = Setting::where('group', 'language')->first();

        if ($locale) {
            $locale =  $locale->data;
            if (isset($locale)) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
