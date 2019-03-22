<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeArea extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_areas';

    protected $fillable = [
        'name'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function processes()
    {
        return $this->belongsToMany(EmployeeProcess::class, 'sau_process_area')->withPivot('employee_headquarter_id');
    }
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_area_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\IndustrialSecure\DangerMatrix', 'employee_area_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
