<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Carbon\Carbon;
use Exception;

class Report extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ph_reports';
    
    protected $fillable = [
        'company_id',
        'observation',
        'image_1',
        'image_2',
        'image_3',
        'rate',
        'condition_id',
        'user_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_process_id',
        'employee_area_id',
        'other_condition'
    ];

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

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class)->where([['id', '<>', '98'],['id', '<>', '114']]);
    }

    /**
     * filters checks through the given conditions
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $conditions
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInConditions($query, $conditions, $typeSearch = 'IN')
    {
        if (COUNT($conditions) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.condition_id', $conditions);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.condition_id', $conditions);
        }

        return $query;
    }

    /**
     * filters checks through the given conditionTypes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $conditionTypes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInConditionTypes($query, $conditionTypes, $typeSearch = 'IN')
    {
        if (COUNT($conditionTypes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_conditions.condition_type_id', $conditionTypes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_conditions.condition_type_id', $conditionTypes);
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
            $query->whereBetween('sau_ph_reports.created_at', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given states
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $states
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInStates($query, $states, $typeSearch = 'IN')
    {
        if (COUNT($states) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_action_plans_activities.state', $states);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_action_plans_activities.state', $states);
        }

        return $query;
    }
}
