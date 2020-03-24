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

    public function employees()
    {
        return $this->hasMany(ContractWorker::class, 'contract_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_user_information_contract_lessee', 'information_id');
    }

    public function listCheckResumen()
    {
        return $this->hasMany(ListCheckResumen::class, 'contract_id');
    }

    public function listCheckHistory()
    {
        return $this->hasMany(ListCheckChangeHistory::class, 'contract_id');
    }

    public function highRiskType()
    {
        return $this->belongsToMany(HighRiskType::class, 'sau_ct_contract_high_risk_type', 'contract_id');
    }

    public function responsibles()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_ct_contract_responsibles', 'contract_id');
    }

    public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_contracts_activities', 'contract_id', 'activity_id');
    }

    public function multiselect()
    {
        return [
            'name' => "{$this->nit} - {$this->social_reason}",
            'value' => $this->id
        ];
    }

    /**
     * filters checks through the given range
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $ranges
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeRangePercentageCumple($query, $range)
    {
        if ($range != '')
        {
            $range = explode('_', $range);
            
            if ($range[0] != '' && $range[1] != '')
            {
                $query->whereBetween('sau_ct_list_check_resumen.total_p_c', $range);
            }
            else if ($range[0] != '' && $range[1] == '')
            {
                $query->where('sau_ct_list_check_resumen.total_p_c', '>=', $range[0]);
            }
            else if ($range[0] == '' && $range[1] != '')
            {
                $query->where('sau_ct_list_check_resumen.total_p_c', '<=', $range[1]);
            }
        }

        return $query;
    }
}
