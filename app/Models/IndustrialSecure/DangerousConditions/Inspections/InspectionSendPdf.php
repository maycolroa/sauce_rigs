<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionSendPdf extends Model
{   
    public $table = 'sau_ph_qualification_inspection_send_pdf';

    protected $fillable = [
        'company_id',
        'email',
        'token',
        'qualification_date'
    ];
}