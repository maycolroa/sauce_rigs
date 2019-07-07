<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class CompanyIntetest extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_company_interest';

    protected $fillable = [
        'company_id',
        'interest_id'
    ];
}
