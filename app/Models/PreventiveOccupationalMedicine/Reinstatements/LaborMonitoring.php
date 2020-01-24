<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class LaborMonitoring extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_labor_monitorings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'set_at',
        'conclusion',
        'check_id',
        'has_monitoring_content',
        'productivity'
    ];
}
