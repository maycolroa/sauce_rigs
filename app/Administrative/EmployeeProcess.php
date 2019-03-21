<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeProcess extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_processes';

    protected $fillable = [
        'name'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function headquarters()
    {
        return $this->belongsToMany(EmployeeHeadquarter::class, 'sau_headquarter_process');
    }

    public function areas()
    {
        return $this->belongsToMany(EmployeeArea::class, 'sau_process_area')->withPivot('employee_headquarter_id');
    }
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_process_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\IndustrialSecure\DangerMatrix', 'employee_process_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
