<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class RestrictionDefault extends Model
{

    protected $table = 'sau_reinc_restrictions_default';

    protected $fillable = [
        'name',
    ];
}
