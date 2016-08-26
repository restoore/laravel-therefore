<?php

namespace Restoore\Therefore;

use Illuminate\Support\ServiceProvider;

class ThereforeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/therefore.php' => config_path('therefore.php')], 'config');
        $this->publishes([__DIR__ . '/../migrations/' => database_path('migrations')], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Restoore\Therefore\Therefore::class, function ($app) {
            return new \Restoore\Therefore\Therefore(config('therefore.wsdl'), ['login' => config('therefore.login'), 'password' => config('therefore.password')]);
        });
        $this->app->bind('therefore', 'Restoore\Therefore\Therefore');
    }
}
