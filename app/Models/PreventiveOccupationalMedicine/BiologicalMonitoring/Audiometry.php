<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\CompanyTrait;

class Audiometry extends Model
{
    use CompanyTrait;

    protected $table = 'sau_bm_audiometries';

    protected $fillable = [
      'date',
      'type',
      'previews_events',
      'employee_id',
      'exposition_level',
      'air_left_500',
      'air_left_1000',
      'air_left_2000',
      'air_left_3000',
      'air_left_4000',
      'air_left_6000',
      'air_left_8000',
      'air_right_500',
      'air_right_1000',
      'air_right_2000',
      'air_right_3000',
      'air_right_4000',
      'air_right_6000',
      'air_right_8000',
      'osseous_left_500',
      'osseous_left_1000',
      'osseous_left_2000',
      'osseous_left_3000',
      'osseous_left_4000',
      'osseous_right_500',
      'osseous_right_1000',
      'osseous_right_2000',
      'osseous_right_3000',
      'osseous_right_4000',
      'recommendations',
      'observation',
      'epp',
      'created_at',
      'updated_at',
      'gap_left',
      'gap_right',
      'air_left_pta',
      'air_right_pta',
      'osseous_left_pta',
      'osseous_right_pta',
      'severity_grade_air_left_pta',
      'severity_grade_air_right_pta',
      'severity_grade_osseous_left_pta',
      'severity_grade_osseous_right_pta',
      'severity_grade_air_left_4000',
      'severity_grade_air_right_4000',
      'severity_grade_osseous_left_4000',
      'severity_grade_osseous_right_4000',
      'severity_grade_air_left_6000',
      'severity_grade_air_right_6000',
      'severity_grade_air_left_8000',
      'severity_grade_air_right_8000',
      'base_type',
      'base',
      'base_state'
    ];

    protected $dates = [
      'created_at',
      'updated_at',
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees';

    public function employee(){
        return $this->belongsTo('App\Models\Administrative\Employees\Employee','employee_id');
    }

    /**
     * Set the epp separate with comma.
     *
     * @param  string  $value
     * @return void
     */
    public function setEppAttribute($value)
    {
      if($value != null)
      {
        $epp = [];

        if (is_array($value)) //Formulario
        {
          foreach($value as $v)
          {
            array_push($epp,json_decode($v)->value);
          }
          
          $this->attributes['epp'] = implode(",", $epp);
        }
        else //Excel
        {
          $this->attributes['epp'] = implode(",", array_map('trim', explode(",", $value))); //Para eliminar espacios en blanco entre valores
        }
      }
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

        if(COUNT($ids) > 0)
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
     * filters checks through the given businesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $businesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInBusinesses($query, $businesses, $typeSearch = 'IN')
    {
        if (COUNT($businesses) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_business_id', $businesses);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_business_id', $businesses);
        }

        return $query;
    }

    /**
     * filters checks through the given positions
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $positions
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPositions($query, $positions, $typeSearch = 'IN')
    {
        if (COUNT($positions) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_position_id', $positions);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_position_id', $positions);
        }
        
        return $query;
    }

    /**
     * filters checks through the given years
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $years
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInYears($query, $years, $typeSearch = 'IN')
    {
        if (COUNT($years) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereRaw("year(sau_bm_audiometries.date) IN (".$years->implode(',').")");

            else if ($typeSearch == 'NOT IN')
                $query->whereRaw("year(sau_bm_audiometries.date) NOT IN (".$years->implode(',').")");
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
            $query->whereBetween('sau_bm_audiometries.date', $dates);
            return $query;
        }
    }
}