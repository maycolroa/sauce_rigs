<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class QualificationColorDinamic extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_qualifications_color_dinamic';

    protected $fillable = [
        'company_id',
        'sin_calificar',
        'cumple',
        'no_cumple',
        'en_estudio',
        'parcial',
        'no_aplica',
        'informativo'
    ];
}
