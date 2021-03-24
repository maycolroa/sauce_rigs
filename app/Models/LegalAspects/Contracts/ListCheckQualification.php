<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ListCheckQualification extends Model
{
    protected $table = 'sau_ct_list_check_qualifications';

    protected $fillable = [
        'contract_id',
        'company_id',
        'user_id',
        'validity_period',
        'state'
    ];


    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function items()
    {
        return $this->hasMany(EvaluationContractItem::class, 'list_qualification_id');
    }
}