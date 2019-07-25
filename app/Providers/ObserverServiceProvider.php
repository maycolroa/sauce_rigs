<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryObserver;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use App\Observers\LegalAspects\LegalMatrix\ArticleObserver;
use App\Models\LegalAspects\LegalMatrix\Article;

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
      Article::observe(ArticleObserver::class);
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
