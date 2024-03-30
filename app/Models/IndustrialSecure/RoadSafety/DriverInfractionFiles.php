<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class DriverInfractionFiles extends Model
{
    protected $table = 'sau_rs_driver_infractionse_files';
    
    protected $fillable = [
        'infraction_id',
        'file'
    ];
}