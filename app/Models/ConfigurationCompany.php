<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ConfigurationCompany extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_configuration_company';

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'observation'
    ];
}
