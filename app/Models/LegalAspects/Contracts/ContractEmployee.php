<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ContractEmployee extends Model
{

    protected $table = 'sau_ct_contract_employees';
    
    protected $fillable = [
        'contract_id',
        'name',
        'identification',
        'position',
        'email'
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