<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ContractLesseeInformation extends Model
{
    protected $table = 'sau_ct_information_contract_lessee';

    protected $fillable = [
        'id',
        'company_id',
        'nit',
        'type',
        'classification',
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
        'created_at',
        'updated_at'
    ];


    public function users(){
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_users');
    }

    public function usersContract(){
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_user_information_contract_lessee');
    }

    public function multiselect()
    {
        return [
            'name' => $this->nit,
            'value' => $this->id
        ];
    }
}
