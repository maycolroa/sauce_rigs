<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeRegional extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_regionals';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_regional_id');
    }

    public function headquarters()
    {
        return $this->hasMany(EmployeeHeadquarter::class, 'employee_regional_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\IndustrialSecure\DangerMatrix', 'employee_regional_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
