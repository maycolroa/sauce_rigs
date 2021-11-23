<?php

namespace App\Models\Administrative\Positions;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeePosition extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_positions';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_position_id');
    }

    public function elements()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\Element', 'sau_employee_position_epp_elements');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
