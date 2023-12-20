<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class LawActionPlan extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_laws_config_action_plan';

    protected $fillable = [
        'company_id',
        'law_id',
        'action_plan'
    ];
}
