<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ContractLessee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_information_contract_lessee';

    protected $fillable = [
        'company_id',
        'nit',
        'classification',
        'type',
        'business_name',
        'phone',
        'address',
        'legal_representative_name',
        'environmental_management_name',
        'economic_activity_of_company',
        'arl',
        'SG_SST_name',
        'risk_class',
        'number_workers',
        'high_risk_work',
        'social_reason',
    ];

    public function multiselect()
    {
        return [
            'name' => "{$this->nit} - {$this->social_reason}",
            'value' => $this->id
        ];
    }
}