<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class MedicalMonitoring extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_medical_monitorings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'set_at',
        'conclusion',
        'check_id'
    ];
}
