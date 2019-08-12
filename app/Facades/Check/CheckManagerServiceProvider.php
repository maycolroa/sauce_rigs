<?php

namespace App\Facades\Check;

use Illuminate\Support\ServiceProvider;

class CheckManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('CheckManager', CheckManager::class);
        $this->app->singleton('CheckManager', function () {
            return new CheckManager;
        });
    }
}
