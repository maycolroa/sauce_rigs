<?php

namespace App\IndustrialSecure;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ConfigQualificationMethodologie extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dm_config_qualification_methodologies';

    protected $fillable = [
        'company_id',
        'types',
        'qualifications'
    ];
}
