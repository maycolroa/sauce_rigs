<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Employee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees';

    public function audiometries(){
        return $this->hasMany('App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry','employee_id');
    }

    public function multiselect(){
      return [
        'name' => "{$this->identification} - {$this->name}",
        'value' => $this->id
      ];
    }
}
