<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Employee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees';

    protected $fillable = [
      'name',
      'date_of_birth',
      'sex',
      'identification',
      'email',
      'employee_area_id',
      'employee_position_id',
      'employee_regional_id',
      'employee_eps_id',
      'income_date',
      'company_id',
      'employee_headquarter_id',
      'employee_process_id',
      'employee_business_id'
  ];

    public function audiometries(){
        return $this->hasMany('App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry','employee_id');
    }

    public function multiselect(){
      return [
        'name' => "{$this->identification} - {$this->name}",
        'value' => $this->id
      ];
    }

    public function setSexAttribute($value)
    {
      $this->attributes['sex'] = strtoupper($value);
    }

    public function regional()
    {
        return $this->belongsTo(EmployeeRegional::class, 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo(EmployeeHeadquarter::class, 'employee_headquarter_id');
    }

    public function area()
    {
        return $this->belongsTo(EmployeeArea::class, 'employee_area_id');
    }

    public function process()
    {
        return $this->belongsTo(EmployeeArea::class, 'employee_process_id');
    }

    public function eps()
    {
        return $this->belongsTo(EmployeeEPS::class, 'employee_eps_id');
    }

    public function business()
    {
        return $this->belongsTo(EmployeeBusiness::class, 'employee_business_id');
    }

    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'employee_position_id');
    }
}
