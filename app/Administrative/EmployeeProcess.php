<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeProcess extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_processes';

    protected $fillable = [
        'name',
        'employee_area_id'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function area()
    {
        return $this->belongsTo(EmployeeArea::class, 'employee_area_id');
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
