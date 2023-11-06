<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class TypeInspectionsItems extends Model
{
    protected $table = 'sau_ph_inspetions_type_items';

    protected $fillable = [
        'type',
        'description'
    ];
}
