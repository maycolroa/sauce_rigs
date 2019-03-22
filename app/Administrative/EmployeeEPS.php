<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;

class EmployeeEPS extends Model
{
    protected $table = 'sau_employees_eps';

    public function multiselect()
    {
        return [
            'name' => $this->code.'-'.$this->name,
            'value' => $this->id
        ];
    }
}
