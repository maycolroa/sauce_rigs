<?php

namespace App\Models\Administrative\Regionals;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeRegional extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_regionals';

    protected $fillable = [
        'name',
        'company_id',
        'abbreviation'
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_regional_id');
    }

    public function headquarters()
    {
        return $this->hasMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_regional_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\Models\IndustrialSecure\DangerMatrix\DangerMatrix', 'employee_regional_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
