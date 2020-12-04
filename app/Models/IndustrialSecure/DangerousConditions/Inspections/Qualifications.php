<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class Qualifications extends Model
{
    protected $table = 'sau_ph_qualifications_inspections';

    protected $fillable = [
        'name',
        'description'
    ];
}
