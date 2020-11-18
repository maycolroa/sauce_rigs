<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class AdditionalFields extends Model
{    
    public $table = 'sau_ph_inspections_additional_fields';

    protected $fillable = [
        'name',
        'inspection_id'
    ];
    
    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
}