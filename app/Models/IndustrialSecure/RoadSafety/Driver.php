<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{

    protected $table = 'sau_rs_drivers';
    
    protected $fillable = [
        'employee_id',
        'type_license_id',
        'date_license',
        //'vehicle_id',
        'responsible_id'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }

    public function typeLicense()
    {
        return $this->belongsTo(TagsTypeLicense::class, 'type_license_id');
    }

    public function responsible()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'responsible_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'sau_rs_driver_vehicles', 'driver_id','vehicle_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->employee->name,
            'value' => $this->id
        ];
    }
}