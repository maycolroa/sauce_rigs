<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ContractLesseeInformation extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_information_contract_lessee';

    protected $fillable = [
        'company_id',
        'nit',
        'active',
        'completed_registration',
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
        'social_reason'
    ];

    /*public function users(){
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_users');
    }*/

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_user_information_contract_lessee', 'information_id');
    }

    public function listCheckResumen()
    {
        return $this->hasMany(LiskCheckResumen::class, 'contract_id');
    }

    public function multiselect()
    {
        return [
            'name' => "{$this->nit} - {$this->social_reason}",
            'value' => $this->id
        ];
    }
}
