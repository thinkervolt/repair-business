<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
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
