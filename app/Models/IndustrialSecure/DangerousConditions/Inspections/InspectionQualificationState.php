<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionQualificationState extends Model
{    
    public $table = 'sau_ph_inspection_qualification_state';

    protected $fillable = [
        'qualification_date',
        'state',
        'motive'
    ];
}