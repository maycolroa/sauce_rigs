<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class ArticleFulfillmentHistory extends Model
{
    protected $table = 'sau_lm_articles_fulfillment_histories';

    protected $fillable = [
        'fulfillment_id',
        'user_id',
        'fulfillment_value',
        'observations'
    ];
}
