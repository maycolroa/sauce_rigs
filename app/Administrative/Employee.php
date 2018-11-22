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
      'company_id'
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
}
