<?php

namespace ApiMiddleware\ApiDebug;

use Illuminate\Support\ServiceProvider;

class ApiDebugServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Middleware'ni avtomatik ro'yxatga olish
        $this->app['router']->aliasMiddleware('apidebug', \ApiMiddleware\ApiDebug\Middleware\ApiDebug::class);
    }
}
