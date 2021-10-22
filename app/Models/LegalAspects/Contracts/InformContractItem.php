<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class InformContractItem extends Model
{
    protected $table = 'sau_ct_inform_contract_items';

    public $timestamps = false;
    
    protected $fillable = [
        'inform_id',
        'item_id',
        'value_programed',
	    'value_executed'
    ];
}