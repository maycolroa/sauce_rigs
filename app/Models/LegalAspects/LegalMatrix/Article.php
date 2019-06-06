<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'sau_lm_articles';

    protected $fillable = [
        'name',
        'description',
        'law_id',
        'repelead',
        'sequence'
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'sau_lm_article_interest');
    }
}
