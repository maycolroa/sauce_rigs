<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ItemQualificationContractDetail extends Model
{
    protected $table = 'sau_ct_item_qualification_contract';

    protected $fillable = [
        'item_id',
        'qualification_id',
        'contract_id',
        'observations',
        'list_qualification_id',
        'state_aprove_qualification',
        'reason_rejection'
    ];

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }
}