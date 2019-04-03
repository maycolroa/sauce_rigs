<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class EvaluationItemRating extends Model
{
    protected $table = 'sau_ct_evaluation_item_rating';

    public $timestamps = false;
    
    protected $fillable = [
        'evaluation_id',
        'item_id',
        'type_rating_id',
        'value'
    ];
}