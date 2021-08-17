<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $table = 'sau_bm_evaluations_criterion';

    protected $fillable = [
        'evaluation_stage_id',
        'description'
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'evaluation_criterion_id');
    }

    public function stages()
    {
        return $this->belongsTo(Stage::class, 'evaluation_stage_id');
    }
}