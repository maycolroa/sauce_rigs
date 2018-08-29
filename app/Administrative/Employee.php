<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Employee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees';

    public function audiometries(){
        return $this->hasMany('App\BiologicalMonitoring\Audiometry','employee_id');
    }
}
