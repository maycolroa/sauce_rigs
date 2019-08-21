<?php

namespace App\Models\Administrative\Processes;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeProcess extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_processes';

    protected $fillable = [
        'name',
        'types'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function headquarters()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_headquarter_process');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Administrative\Areas\EmployeeArea', 'sau_process_area')->withPivot('employee_headquarter_id');
    }
    
    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_process_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\Models\IndustrialSecure\DangerMatrix\DangerMatrix', 'employee_process_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
