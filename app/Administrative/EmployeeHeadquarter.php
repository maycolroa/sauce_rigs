<?php

namespace App\Administrative;

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
        return $this->belongsTo(EmployeeRegional::class, 'employee_regional_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
