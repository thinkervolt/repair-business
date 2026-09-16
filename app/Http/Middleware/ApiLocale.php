<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;

class ApiLocale
{
    public function handle($request, Closure $next)
    {
        $locale = substr((string) $request->header('Accept-Language', ''), 0, 2);

        if (!in_array($locale, ['en', 'es'])) {
            $setting = Setting::where('group', 'language')->first();
            $locale = $setting ? $setting->data : 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}