<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class AdditionalFields extends Model
{    
    use CompanyTrait;

    public $table = 'sau_dm_additional_fields';

    protected $fillable = [
        'name',
        'company_id'
    ];
}