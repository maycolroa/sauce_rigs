<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeBusiness extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_businesses';

    protected $fillable = [
        'name',
        'company_id'
    ];

    /*public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_business_id');
    }*/
}
