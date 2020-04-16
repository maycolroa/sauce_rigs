<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class TrainingEmployeeSend extends Model
{

    protected $table = 'sau_ct_training_employee_send';
    
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
        return $this->hasMany(ContractEmployee::class, 'employee_id');
    }
}