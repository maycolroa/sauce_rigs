<?php

namespace App\Models\Administrative\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HeadquartersScope;
use App\Traits\CompanyTrait;

class Employee extends Model
{
     /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new HeadquartersScope);
    }

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
      'employee_business_id',
      'deal',
      'employee_afp_id',
      'employee_arl_id',
      'contract_numbers',
      'last_contract_date',
      'contract_type',
      'mobile',
      'extension',
      'age',
      'salary',
      'active',
      'date_inactivation'
  ];

    public function audiometries(){
        return $this->hasMany('App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry','employee_id');
    }

    public function reports(){
        return $this->hasMany('App\Models\PreventiveOccupationalMedicine\Reinstatements\Check','employee_id');
    }

    public function multiselect(){
      return [
        'name' => "{$this->identification} - {$this->name}",
        'value' => $this->id
      ];
    }

    public function setSexAttribute($value)
    {
      $this->attributes['sex'] = ucfirst($value);
    }

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

    public function eps()
    {
        return $this->belongsTo(EmployeeEPS::class, 'employee_eps_id');
    }

    public function afp()
    {
        return $this->belongsTo(EmployeeAFP::class, 'employee_afp_id');
    }

    public function arl()
    {
        return $this->belongsTo(EmployeeARL::class, 'employee_arl_id');
    }

    public function business()
    {
        return $this->belongsTo('App\Models\Administrative\Business\EmployeeBusiness', 'employee_business_id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Administrative\Positions\EmployeePosition', 'employee_position_id');
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
     * filters checks through the given deals
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $deals
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDeals($query, $deals, $typeSearch = 'IN')
    {
        if (COUNT($deals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.deal', $deals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.deal', $deals);
        }

        return $query;
    }

    /**
     * filters checks through the given names
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $names
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInNames($query, $names, $typeSearch = 'IN')
    {
        if (COUNT($names) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.name', $names);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.name', $names);
        }

        return $query;
    }

    /**
     * filters checks through the given identifications
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $identifications
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInIdentifications($query, $identifications, $typeSearch = 'IN')
    {
        if (COUNT($identifications) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.identification', $identifications);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.identification', $identifications);
        }

        return $query;
    }

    public function isActive()
    {
        return $this->active == 'SI';
    }

    public function scopeActive($query, $active = true)
    {
        if ($active)
            $query->where('sau_employees.active', 'SI');
        else
            $query->where('sau_employees.active', 'NO');

        return $query;
    }
}
