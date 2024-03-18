<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;

class AdditionalFields extends Model
{    
    public $table = 'sau_rs_inspections_additional_fields';

    protected $fillable = [
        'name',
        'inspection_id'
    ];
    
    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
}