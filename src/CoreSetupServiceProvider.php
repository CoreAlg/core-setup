<?php

namespace CoreSetup;

use Illuminate\Support\ServiceProvider;

class CoreSetupServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path(''),
        ]);

        $this->publishes([
            __DIR__.'/../assets' => public_path('vendor/core-setup'),
        ], 'public');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../app' => app_path('')
        ], 'models');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        
    }
}