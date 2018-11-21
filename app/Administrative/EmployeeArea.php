<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EmployeeArea extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees_areas';

    protected $fillable = [
        'name',
        'company_id'
    ];
}
