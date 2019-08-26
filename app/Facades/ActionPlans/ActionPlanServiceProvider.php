<?php

namespace App\Facades\ActionPlans;

use Illuminate\Support\ServiceProvider;

class ActionPlanServiceProvider extends ServiceProvider
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
        $this->app->alias('ActionPlan', ActionPlan::class);
        $this->app->singleton('ActionPlan', function () {
            return new ActionPlan;
        });
    }
}
