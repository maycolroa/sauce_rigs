<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class EvaluationPerformItem extends Model
{
    protected $table = 'sau_bm_evaluation_perform_items';

    public $timestamps = false;
    
    protected $fillable = [
        'evaluation_id',
        'item_id',
        'value'
    ];
}