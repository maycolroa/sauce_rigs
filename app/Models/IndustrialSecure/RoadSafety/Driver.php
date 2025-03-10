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
        'responsible'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }

    public function typeLicense()
    {
        return $this->belongsTo(TagsTypeLicense::class, 'type_license_id');
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

    public function scopeInDrivers($query, $drivers, $typeSearch = 'IN')
    {
        if (COUNT($drivers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_drivers.id', $drivers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_drivers.id', $drivers);
        }

        return $query;
    }

    public function scopeInVehicles($query, $vehicles, $typeSearch = 'IN')
    {
        if (COUNT($vehicles) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_driver_vehicles.vehicle_id', $vehicles);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_driver_vehicles.vehicle_id', $vehicles);
        }

        return $query;
    }

    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_regional_id', $regionals);
        }

        return $query;
    }

    /**
     * filters checks through the given headquarters
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $headquarters
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHeadquarters($query, $headquarters, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($headquarters as $key => $value)
        {
            $ids[] = $value;
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_headquarter_id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given processes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $processes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProcesses($query, $processes, $typeSearch = 'IN')
    {
        if (COUNT($processes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_process_id', $processes);
        }

        return $query;
    }

    /**
     * filters checks through the given areas
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $areas
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInAreas($query, $areas, $typeSearch = 'IN')
    {
        if (COUNT($areas) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_area_id', $areas);
        }

        return $query;
    }

    public function scopeInTypeLicenses($query, $typeLicenses, $typeSearch = 'IN')
    {
        if (COUNT($typeLicenses) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_drivers.type_license_id', $typeLicenses);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_drivers.type_license_id', $typeLicenses);
        }

        return $query;
    }

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_rs_drivers.date_license', $dates);
            return $query;
        }
    }
}