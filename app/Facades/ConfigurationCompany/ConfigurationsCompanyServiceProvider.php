<?php

namespace App\Facades\ConfigurationsCompany;

use Illuminate\Support\ServiceProvider;

class ConfigurationsCompanyServiceProvider extends ServiceProvider
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
        $this->app->alias('ConfigurationsCompany', ConfigurationsCompany::class);
        $this->app->singleton('ConfigurationsCompany', function () {
            return new ConfigurationsCompany;
        });
    }
}
