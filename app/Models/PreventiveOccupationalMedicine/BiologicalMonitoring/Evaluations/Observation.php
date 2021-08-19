<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $table = 'sau_bm_evaluation_item_observations';

    protected $fillable = [
        'evaluation_id',
        'item_id',
        'description'
    ];
}