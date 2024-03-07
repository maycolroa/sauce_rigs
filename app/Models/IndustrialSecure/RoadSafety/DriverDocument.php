<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    protected $table = 'sau_rs_drivers_documents';

    //public $timestamps = false;
    
    protected $fillable = [
        'driver_id',
        'name',
        'file'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}