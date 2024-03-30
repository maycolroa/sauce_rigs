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
        'date_simit'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function typeInfraction()
    {
        return $this->belongsTo(DriverInfractionType::class, 'type_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function codesInfractions()
    {
        return $this->belongsToMany(DriverInfractionTypeCode::class, 'sau_rs_driver_infractions_codes', 'infraction_id', 'code_id');
    }

    public function files()
    {
        return $this->hasMany(DriverInfractionFiles::class, 'infraction_id');
    }
}