<?php

namespace App\Observers\LegalAspects\LegalMatrix;

use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use Illuminate\Support\Facades\Auth;

class ArticleFulfillmentObserver
{
    /**
     * Handle the article "saving" event.
     *
     * @param  \App\Models\LegalAspects\LegalMatrix\ArticleFulfillment $article
     * @return void
     */
    public function updating(ArticleFulfillment $article)
    {
        $article->histories()->create([
            'user_id' => Auth::user()->id,
            'fulfillment_value' => $article->fulfillment_value ? $article->fulfillment_value->name : NULL,
            'observations' => $article->observations
        ]);
    }
}
