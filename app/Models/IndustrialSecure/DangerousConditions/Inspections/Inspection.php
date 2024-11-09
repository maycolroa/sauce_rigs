<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Inspection extends Model
{
    use CompanyTrait;
    
    public $table = 'sau_ph_inspections';

    protected $fillable = [
        'name',
        'company_id',
        'state',
        'type_id',
        'fullfilment_parcial',
        'version',
        'description'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function type()
    {
        return $this->belongsTo(TypeInspections::class);
    }

    public function themes()
    {
        return $this->hasMany(InspectionSection::class, 'inspection_id');
    }
    
    public function additional_fields()
    {
        return $this->hasMany(AdditionalFields::class, 'inspection_id');
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->state == 'SI';
    }

    public function regionals()
    {
        return $this->belongsToMany('App\Models\Administrative\Regionals\EmployeeRegional', 'sau_ph_inspection_regional');
    }

    public function headquarters()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_ph_inspection_headquarter');
    }

    public function processes()
    {
        return $this->belongsToMany('App\Models\Administrative\Processes\EmployeeProcess', 'sau_ph_inspection_process');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Administrative\Areas\EmployeeArea', 'sau_ph_inspection_area');
    }

    /**
     * filters checks through the given type
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $types
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInTypes($query, $types, $typeSearch = 'IN')
    {
        if (COUNT($types) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspections.type_id', $types);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspections.type_id', $types);
        }

        return $query;
    }

    /**
     * filters checks through the given regionals
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $regionals
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_regional.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_regional.employee_regional_id', $regionals);
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
                $query->whereIn('sau_ph_inspection_headquarter.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_headquarter.employee_headquarter_id', $ids);
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
                $query->whereIn('sau_ph_inspection_process.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_process.employee_process_id', $processes);
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
                $query->whereIn('sau_ph_inspection_area.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_area.employee_area_id', $areas);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_ph_inspections.created_at', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given inspections
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $inspections
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInInspections($query, $inspections, $typeSearch = 'IN')
    {
        if (COUNT($inspections) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspections.id', $inspections);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspections.id', $inspections);
        }

        return $query;
    }
}