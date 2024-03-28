<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class DriverInfraction extends Model
{

    protected $table = 'sau_rs_driver_infractions';
    
    protected $fillable = [
        'company_id',
        'driver_id',
        'vehicle_id',
        'type_id',
        'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function typeInfraction()
    {
        return $this->belongsTo(DriverInfractionType::class, 'type_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_id');
    }
}