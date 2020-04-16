<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ContractEmployee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_contract_employees';
    
    protected $fillable = [
        'contract_id',
        'name',
        'identification',
        'position',
        'email',
        'company_id',
        'token'
    ];

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_contract_employee_activities', 'employee_id', 'activity_contract_id');
    }
}