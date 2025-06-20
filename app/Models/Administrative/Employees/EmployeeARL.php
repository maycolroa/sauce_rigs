<?php

namespace App\Models\Administrative\Employees;

use Illuminate\Database\Eloquent\Model;

class EmployeeARL extends Model
{
    protected $table = 'sau_employees_arl';

    protected $fillable = [
        'name',
        'code',
        'state'
    ];
    
    public function multiselect()
    {
        return [
            'name' => $this->code.'-'.$this->name,
            'value' => $this->id
        ];
    }
}
