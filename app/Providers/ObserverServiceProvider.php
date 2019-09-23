<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryObserver;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use App\Observers\LegalAspects\LegalMatrix\ArticleObserver;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Observers\LegalAspects\LegalMatrix\ArticleFulfillmentObserver;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Observers\LegalAspects\Contracts\ItemQualificationContractDetailObserver;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;

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
      ArticleFulfillment::observe(ArticleFulfillmentObserver::class);
      ItemQualificationContractDetail::observe(ItemQualificationContractDetailObserver::class);
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
