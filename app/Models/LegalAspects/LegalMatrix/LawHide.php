<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class LawHide extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_laws_hide_companies';

    protected $fillable = [
        'company_id',
        'law_id',
        'user_id'
    ];
}
