<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

use App\Models\Notification;

class Notifications
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
        $notifications = Notification::take(3)->get(); 
        $notifications_count = Notification::count(); 
        View::share('notifications', $notifications);
        View::share('notifications_count', $notifications_count);
        return $next($request);
    } 
}
