<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Vehicle extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rs_vehicles';
    
    protected $fillable = [
        'company_id',
        'plate',
        'name_propietary',
        'registration_number',
        'registration_number_date',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_area_id',
        'employee_process_id',
        'type_vehicle',
        'code_vehicle',
        'mark',
        'line',
        'model',
        'cylinder_capacity',
        'color',
        'chassis_number',
        'engine_number',
        'passenger_capacity',
        'loading_capacity',
        'state',
        'soat_number',
        'insurance',
        'expedition_date_soat',
        //'required_due_date_soat',
        'due_date_soat',
        'file_soat',
        'mechanical_tech_number',
        'issuing_entity',
        'expedition_date_mechanical_tech',
        //'required_due_date_mechanical_tech',
        'due_date_mechanical_tech',
        'file_mechanical_tech',
        'policy_responsability',
        'policy_number',
        'policy_entity',
        'expedition_date_policy',
        //'required_due_date_policy',
        'due_date_policy',
        'file_policy',
    ];

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_headquarter_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'employee_area_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'employee_process_id');
    }

    public function histories()
    {
        return $this->hasMany(HistoryChanges::class, 'vehicle_id');
    }

    /*public function position()
    {
        return $this->belongsTo('App\Models\Administrative\Positions\EmployeePosition', 'employee_position_id');
    }*/

    public function multiselect()
    {
        return [
            'name' => $this->plate,
            'value' => $this->id
        ];
    }

    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_vehicles.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_vehicles.employee_regional_id', $regionals);
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
                $query->whereIn('sau_rs_vehicles.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_vehicles.employee_headquarter_id', $ids);
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
                $query->whereIn('sau_rs_vehicles.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_vehicles.employee_process_id', $processes);
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
                $query->whereIn('sau_rs_vehicles.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_vehicles.employee_area_id', $areas);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDateSoat($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_rs_vehicles.due_date_soat', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDateTm($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_rs_vehicles.due_date_mechanical_tech', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDateRc($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_rs_vehicles.due_date_policy', $dates);
            return $query;
        }
    }

    public function scopeInTypeVehicle($query, $typeVehicle, $typeSearch = 'IN')
    {
        $regexp = [];

        foreach ($typeVehicle as $key => $value)
        {
            $regexp[] = "((^|,)($value)(,|$))";
        }

        if (COUNT($regexp) > 0)
        {
            $regexp = implode("|", $regexp);

            if ($typeSearch == 'IN')
                $query->where('sau_rs_vehicles.type_vehicle', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_rs_vehicles.type_vehicle', 'NOT REGEXP', $regexp);
        }

        return $query;
    }
}