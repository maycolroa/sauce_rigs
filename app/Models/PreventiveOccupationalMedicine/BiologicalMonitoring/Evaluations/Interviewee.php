<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class Interviewee extends Model
{
    protected $table = 'sau_bm_evaluation_interviewees';

    public $timestamps = false;

    protected $fillable = [
        'evaluation_id',
        'name',
        'position'
    ];
}