<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionQualified extends Model
{
    public $table = 'sau_rs_inspections_qualified';

    protected $fillable = [
        'vehicle_id',
        'inspection_id',
        'company_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_process_id',
        'employee_area_id',
        'qualifier_id',
        'qualification_date'
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function qualifier()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'qualifier_id');
    }
    
    public function vehicle()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\RoadSafety\Vehicle', 'vehicle_id');
    }

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_headquarter_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'employee_process_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'employee_area_id');
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
                $query->whereIn('sau_rs_inspections_qualified.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspections_qualified.employee_regional_id', $regionals);
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
                $query->whereIn('sau_rs_inspections_qualified.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspections_qualified.employee_headquarter_id', $ids);
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
                $query->whereIn('sau_rs_inspections_qualified.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspections_qualified.employee_process_id', $processes);
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
                $query->whereIn('sau_rs_inspections_qualified.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspections_qualified.employee_area_id', $areas);
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
            $query->whereBetween('sau_rs_inspections_qualified.qualification_date', $dates);
            return $query;
        }
    }

     /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInUserFirm($query, $user_firm, $typeSearch = 'IN')
    {
        if (COUNT($user_firm) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_qualification_inspection_firm.user_id', $user_firm);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_qualification_inspection_firm.user_id', $user_firm);
        }

        return $query;
    }

    /**
     * filters checks through the given qualifiers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $qualifiers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInQualifiers($query, $qualifiers, $typeSearch = 'IN')
    {
        if (COUNT($qualifiers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_inspections_qualified.qualifier_id', $qualifiers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspections_qualified.qualifier_id', $qualifiers);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenInspectionDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_rs_inspections.created_at', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given themes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $themes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInThemes($query, $themes, $typeSearch = 'IN', $alias = 'sau_rs_inspection_sections')
    {
        $ids = [];

        foreach ($themes as $key => $value)
        {
            $ids[] = $value;
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn("{$alias}.id", $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn("{$alias}.id", $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given inspections
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $inspections
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInInspections($query, $inspections, $typeSearch = 'IN', $alias = 'sau_rs_inspections')
    {
        if (COUNT($inspections) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn("{$alias}.id", $inspections);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn("{$alias}.id", $inspections);
        }

        return $query;
    }
}
