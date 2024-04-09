<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingEmployeeSend extends Model
{

    protected $table = 'sau_rs_training_employee_send';
    
    protected $fillable = [
        'training_id',
        'employee_id'
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class, 'training_id');
    }

 
    public function employees()
    {
        return $this->hasMany('App\Models\Administrative\Employees\Employee', 'employee_id');
    }
}