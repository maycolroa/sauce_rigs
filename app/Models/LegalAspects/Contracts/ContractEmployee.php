<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use App\Models\Administrative\Employees\EmployeeAFP;
use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\General\Departament;
use App\Models\General\Municipality;

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
        'state',
        'employee_eps_id',
        'sex',    
        'phone_residence',
        'phone_movil',
        'direction',
        'disability_condition',
        'emergency_contact',
        'rh',
        'salary',
        'date_of_birth',
        'disability_description',
        'emergency_contact_phone',
        'workday',
        'civil_status',
        'state_employee',
        'departament_id',
        'city_id',
        'income_date',
        'deadline',
        'motive_inactivation',
        'file_inactivation',
        'liquidated',
        'liquidated_date'
    ];

    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }

    public function city()
    {
        return $this->belongsTo(Municipality::class, 'city_id');
    }

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_contract_employee_activities', 'employee_id', 'activity_contract_id');
    }

    public function proyects()
    {
        return $this->belongsToMany(ProyectContract::class, 'sau_ct_contract_employee_proyects', 'employee_id', 'proyect_contract_id');
    }

    public function afp()
    {
        return $this->belongsTo(EmployeeAFP::class, 'employee_afp_id');
    }

    public function eps()
    {
        return $this->belongsTo(EmployeeEPS::class, 'employee_eps_id');
    }

    public function scopeInProyects($query, $proyects, $typeSearch = 'IN')
    {
        if (COUNT($proyects) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_contract_employee_proyects.proyect_contract_id', $proyects);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_contract_employee_proyects.proyect_contract_id', $proyects);
        }

        return $query;
    }
}