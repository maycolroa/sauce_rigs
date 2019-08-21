<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class ArticleHistory extends Model
{
    protected $table = 'sau_lm_article_histories';

    protected $fillable = [
        'article_id',
        'user_id'
    ];
}
