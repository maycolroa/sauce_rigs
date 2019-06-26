<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ItemQualificationContractDetail extends Model
{
    protected $table = 'sau_ct_item_qualification_contract';
    
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'qualification_id',
        'contract_id'
    ];
}