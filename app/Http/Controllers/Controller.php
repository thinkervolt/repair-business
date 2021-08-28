<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

        if(getenv('DB_CONNECTION') AND getenv('DB_DATABASE')){
            if (Schema::hasTable('notifications')){
                $notifications = \App\Notification::take(3)->get(); 
                $notifications_count = \App\Notification::count(); 
                View::share('notifications', $notifications);
                View::share('notifications_count', $notifications_count);
            }
        }

    }


}
