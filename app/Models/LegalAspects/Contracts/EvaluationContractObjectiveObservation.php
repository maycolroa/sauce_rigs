<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class EvaluationContractObjectiveObservation extends Model
{
    protected $table = 'sau_ct_evaluation_contract_objective_observations';
    
    protected $fillable = [
        'evaluation_id',
        'objective_id',
        'observation'
    ];
}