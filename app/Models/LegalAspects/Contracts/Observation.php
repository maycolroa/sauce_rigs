<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $table = 'sau_ct_item_observations';

    protected $fillable = [
        'evaluation_id',
        'item_id',
        'description'
    ];
}