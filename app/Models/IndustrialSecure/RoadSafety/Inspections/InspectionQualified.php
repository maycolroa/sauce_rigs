<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionQualified extends Model
{
    public $table = 'sau_rs_inspections_qualified';

    protected $fillable = [
        'vehicle_id',
        'inspection_id',
        'company_id',
        'qualifier_id',
        'qualification_date'
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function qualifier()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'qualifier_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
