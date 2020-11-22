<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class AdditionalFieldsValues extends Model
{    
    public $table = 'sau_ph_inspections_additional_fields_values';

    protected $fillable = [
        'field_id',
        'value',
        'qualification_date'
    ];
    
    public function fields()
    {
        return $this->belongsTo(AdditionalFields::class);
    }
}