<?php

namespace Hilsonxhero\Xauth\Providers;

use Hilsonxhero\Xauth\Xauth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function register()
    {
        $this->app->bind('Xauth', function () {
            return new Xauth;
        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'Xauth');
        $this->mergeConfigFrom(__DIR__ . '/../config/xauth.php', 'xauth');

        $this->publishes([
            __DIR__ . '/../config/xauth.php' => config_path('xauth.php')
        ], 'xauth-config');
    }
}
