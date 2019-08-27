<?php

namespace App\Models\Administrative\Employees;

use Illuminate\Database\Eloquent\Model;

class EmployeeAFP extends Model
{
    protected $table = 'sau_employees_afp';

    public function multiselect()
    {
        return [
            'name' => $this->code.'-'.$this->name,
            'value' => $this->id
        ];
    }
}
