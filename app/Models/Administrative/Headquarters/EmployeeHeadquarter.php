<?php

namespace App\Models\Administrative\Headquarters;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeHeadquarter extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_employees_headquarters';

    protected $fillable = [
        'name',
        'employee_regional_id'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function processes()
    {
        return $this->belongsToMany('App\Models\Administrative\Processes\EmployeeProcess', 'sau_headquarter_process');
    }

    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_headquarter_id');
    }

    public function dangerMatrices()
    {
        return $this->hasMany('App\Models\IndustrialSecure\DangerMatrix\DangerMatrix', 'employee_headquarter_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_reinc_user_headquarter');
    }
}
