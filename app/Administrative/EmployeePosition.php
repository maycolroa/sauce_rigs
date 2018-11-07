<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeePosition extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_positions';

    protected $fillable = [
        'name',
        'company_id'
    ];
}
