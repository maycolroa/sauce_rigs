<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Services\ConfigurationServices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(250);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton('configuration_service', function () {
        return new ConfigurationServices();
      });
    }
}
