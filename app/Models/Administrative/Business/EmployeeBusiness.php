<?php

namespace App\Models\Administrative\Business;

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

    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_business_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
