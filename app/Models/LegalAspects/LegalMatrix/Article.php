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
        'repealed',
        'sequence'
    ];

    public function law()
    {
        return $this->belongsTo(Law::class);
    }
}
