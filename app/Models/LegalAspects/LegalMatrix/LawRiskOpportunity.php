<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class LawRiskOpportunity extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_law_risk_opportunity';

    protected $fillable = [
        'company_id',
        'law_id',
        'user_id',
        'type',
        'risk',
        'description',
        'type_risk',
        'risk_subsystem',
        'risk_gestion',
        'risk_id_text',
        'description_no_apply'
    ];
}
