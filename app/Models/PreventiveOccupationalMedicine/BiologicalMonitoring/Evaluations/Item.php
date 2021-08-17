<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'sau_bm_evaluations_items';

    protected $fillable = [
        'evaluation_criterion_id',
        'description'
    ];

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'evaluation_criterion_id');
    }
}