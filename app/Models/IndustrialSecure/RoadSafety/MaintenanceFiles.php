<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class MaintenanceFiles extends Model
{
    protected $table = 'sau_rs_vehicle_maintenance_files';
    
    protected $fillable = [
        'vehicle_maintenance_id',
        'file'
    ];
}