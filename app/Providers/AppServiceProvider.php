<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('PROXY_SCHEMA') === 'https') {
            $this->app['request']->server->set('HTTPS', true);
        }else{
            $this->app['request']->server->set('HTTP', true);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceRootUrl(env("PROXY_URL"));
        URL::forceScheme(env("PROXY_SCHEMA"));

    }
}
