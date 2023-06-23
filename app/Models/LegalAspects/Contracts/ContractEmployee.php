<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use App\Models\Administrative\Employees\EmployeeAFP;

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
        'token',
        'employee_afp_id',
        'state'
    ];

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_contract_employee_activities', 'employee_id', 'activity_contract_id');
    }

    public function afp()
    {
        return $this->belongsTo(EmployeeAFP::class, 'employee_afp_id');
    }
}