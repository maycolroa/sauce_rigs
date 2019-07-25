<?php

namespace App\Observers\LegalAspects\LegalMatrix;

use App\Models\LegalAspects\LegalMatrix\Article;
use Illuminate\Support\Facades\Auth;

class ArticleObserver
{
    /**
     * Handle the article "saving" event.
     *
     * @param  \App\Models\LegalAspects\LegalMatrix\Article $article
     * @return void
     */
    public function updating(Article $article)
    {
        $article->histories()->create([
            'user_id' => Auth::user()->id
        ]);
    }
}
