<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class TypeInspections extends Model
{
    protected $table = 'sau_ph_type_inspections';

    protected $fillable = [
        'type',
        'description'
    ];
}
