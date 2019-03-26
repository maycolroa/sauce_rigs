<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ContractLessee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_information_contract_lessee';

    protected $fillable = [
        'nit',
        'type',
        'business_name',
        'phone',
        'address',
        'legal_representative_name',
        'SG_SST_name',
        'number_workers',
        'high_risk_work',
        'social_reason'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->nit,
            'value' => $this->id
        ];
    }
}