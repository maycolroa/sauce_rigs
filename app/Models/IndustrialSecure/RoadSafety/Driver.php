<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{

    protected $table = 'sau_rs_drivers';
    
    protected $fillable = [
        'employee_id',
        'type_license',
        'date_license',
        'vehicle_id',
        'responsible_id'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }

    public function responsible()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'responsible_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}