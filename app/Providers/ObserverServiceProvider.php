<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryObserver;
use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      Audiometry::observe(AudiometryObserver::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
