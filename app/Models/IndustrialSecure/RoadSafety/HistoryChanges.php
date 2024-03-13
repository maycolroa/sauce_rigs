<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class HistoryChanges extends Model
{

    protected $table = 'sau_rs_history_records_vehicles';
    
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'description',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}