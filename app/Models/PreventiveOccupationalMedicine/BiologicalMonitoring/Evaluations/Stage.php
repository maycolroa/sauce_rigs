<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $table = 'sau_bm_evaluations_stages';

    protected $fillable = [
        'evaluation_id',
        'description'
    ];

    public function criterion()
    {
        return $this->hasMany(Criterion::class, 'evaluation_stage_id');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }
}