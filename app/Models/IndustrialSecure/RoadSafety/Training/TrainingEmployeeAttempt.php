<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingEmployeeAttempt extends Model
{

    protected $table = 'sau_rs_training_employee_attempts';
    
    protected $fillable = [
        'attempt',
        'state',
        'training_id',
        'employee_id'
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class, 'training_id');
    }

    public function employees()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }
}