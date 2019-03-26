<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $table = 'sau_ct_item_observations';

    protected $fillable = [
        'item_id',
        'description'
    ];
}