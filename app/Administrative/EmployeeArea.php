<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeArea extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_areas';

    protected $fillable = [
        'name',
        'employee_headquarter_id'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees_regionals';

    public function headquarter()
    {
        return $this->belongsTo(EmployeeHeadquarter::class, 'employee_headquarter_id');
    }

    public function processes()
    {
        return $this->hasMany(EmployeeProcess::class, 'employee_area_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
