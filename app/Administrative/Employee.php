<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Employee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_employees';

    protected $fillable = [
      'name',
      'date_of_birth',
      'sex',
      'identification',
      'email',
      'employee_area_id',
      'employee_position_id',
      'employee_regional_id',
      'employee_eps_id',
      'income_date',
      'company_id',
      'employee_headquarter_id',
      'employee_process_id',
      'employee_business_id'
  ];

    public function audiometries(){
        return $this->hasMany('App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry','employee_id');
    }

    public function multiselect(){
      return [
        'name' => "{$this->identification} - {$this->name}",
        'value' => $this->id
      ];
    }

    public function setSexAttribute($value)
    {
      $this->attributes['sex'] = strtoupper($value);
    }

    public function regional()
    {
        return $this->belongsTo(EmployeeRegional::class, 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo(EmployeeHeadquarter::class, 'employee_headquarter_id');
    }

    public function area()
    {
        return $this->belongsTo(EmployeeArea::class, 'employee_area_id');
    }

    public function process()
    {
        return $this->belongsTo(EmployeeProcess::class, 'employee_process_id');
    }

    public function eps()
    {
        return $this->belongsTo(EmployeeEPS::class, 'employee_eps_id');
    }

    public function business()
    {
        return $this->belongsTo(EmployeeBusiness::class, 'employee_business_id');
    }

    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'employee_position_id');
    }

    public function actionPlanActivities()
    {
        return $this->hasMany('App\Models\ActionPlansActivity', 'employee_id');
    }

    /**
     * filters checks through the given regionals
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $regionals
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRegionals($query, $regionals)
    {
        $query->where(function ($subquery) use ($regionals) {
            foreach ($regionals as $regionalId) {
                $subquery->orWhere('sau_employees.employee_regional_id', $regionalId);
            }
        });
        return $query;
    }

    /**
     * filters checks through the given headquarters
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $headquarters
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHeadquarters($query, $headquarters)
    {
        $query->where(function ($subquery) use ($headquarters) {
            foreach ($headquarters as $headquarterId) {
                $subquery->orWhere('sau_employees.employee_headquarter_id', $headquarterId);
            }
        });
        return $query;
    }

    /**
     * filters checks through the given areas
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $areas
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInAreas($query, $areas)
    {
        $query->where(function ($subquery) use ($areas) {
            foreach ($areas as $areaId) {
                $subquery->orWhere('sau_employees.employee_area_id', $areaId);
            }
        });
        return $query;
    }

    /**
     * filters checks through the given processes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $processes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProcesses($query, $processes)
    {
        $query->where(function ($subquery) use ($processes) {
            foreach ($processes as $processId) {
                $subquery->orWhere('sau_employees.employee_process_id', $processId);
            }
        });
        return $query;
    }

    /**
     * filters checks through the given businesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $businesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInBusinesses($query, $businesses)
    {
        $query->where(function ($subquery) use ($businesses) {
            foreach ($businesses as $businessId) {
                $subquery->orWhere('sau_employees.employee_business_id', $businessId);
            }
        });
        return $query;
    }

    /**
     * filters checks through the given positions
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $positions
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPositions($query, $positions)
    {
        $query->where(function ($subquery) use ($positions) {
            foreach ($positions as $positionId) {
                $subquery->orWhere('sau_employees.employee_position_id', $positionId);
            }
        });
        return $query;
    }
}
