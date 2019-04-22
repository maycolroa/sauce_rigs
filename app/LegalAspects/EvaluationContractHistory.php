<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class EvaluationContractHistory extends Model
{
    protected $table = 'sau_ct_evaluation_contract_histories';

    protected $fillable = [
        'evaluation_id',
        'user_id'
    ];
}