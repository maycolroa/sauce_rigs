<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class InformContractItemObservation extends Model
{
    protected $table = 'sau_ct_inform_contract_item_observations';

    protected $fillable = [
        'inform_id',
        'item_id',
        'description'
    ];
}