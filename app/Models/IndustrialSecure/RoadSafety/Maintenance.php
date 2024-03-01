<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'sau_rs_vehicle_maintenance';
    
    protected $fillable = [
        'vehicle_id',
        'date',
        'type',
        'km',
        'description',
        'responsible',
        'apto',
        'reason',
        'next_date',
    ];

    public function files()
    {
        return $this->hasMany(MaintenanceFiles::class, 'vehicle_maintenance_id');
    }
}