<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class Combustible extends Model
{
    protected $table = 'sau_rs_vehicle_combustibles';
    
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'date',
        'km',
        'cylinder_capacity',
        'quantity_galons',
        'price_galon',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    
}