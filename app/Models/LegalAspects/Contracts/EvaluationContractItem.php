<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class EvaluationContractItem extends Model
{
    protected $table = 'sau_ct_evaluation_contract_items';

    public $timestamps = false;
    
    protected $fillable = [
        'evaluation_id',
        'item_id'
    ];
}