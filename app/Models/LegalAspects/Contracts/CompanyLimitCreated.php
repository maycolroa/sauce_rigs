<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class CompanyLimitCreated extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_company_limit_created';

    protected $fillable = [
        'company_id',
        'value'
    ];
}