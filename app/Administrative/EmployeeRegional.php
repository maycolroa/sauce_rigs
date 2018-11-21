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

    /*public function sedes()
    {
        return $this->hasMany(EmployeeSede::class, 'employee_regional_id');
    }*/
}
