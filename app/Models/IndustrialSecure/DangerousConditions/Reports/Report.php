<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    const DISK = 's3_DConditions';

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

    public function scopeInRates($query, $rates, $typeSearch = 'IN')
    {
        if (COUNT($rates) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.rate', $rates);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.rate', $rates);
        }

        return $query;
    }

    public function scopeInUsers($query, $users, $typeSearch = 'IN')
    {
        if (COUNT($users) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.user_id', $users);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.user_id', $users);
        }

        return $query;
    }

    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.employee_regional_id', $regionals);
        }

        return $query;
    }

    public function scopeInHeadquarters($query, $headquarters, $typeSearch = 'IN')
    {
        if (COUNT($headquarters) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.employee_headquarter_id', $headquarters);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.employee_headquarter_id', $headquarters);
        }

        return $query;
    }

    public function scopeInProcesses($query, $processes, $typeSearch = 'IN')
    {
        if (COUNT($processes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.employee_process_id', $processes);
        }

        return $query;
    }

    public function scopeInAreas($query, $areas, $typeSearch = 'IN')
    {
        if (COUNT($areas) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_reports.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_reports.employee_area_id', $areas);
        }

        return $query;
    }

    public function scopeInYears($query, $years, $typeSearch = 'IN')
    {
        if (COUNT($years) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereRaw('YEAR(sau_ph_reports.created_at) IN (' . $years->implode(',') . ')' );

            else if ($typeSearch == 'NOT IN')
                $query->whereRaw('YEAR(sau_ph_reports.created_at) NOT IN (' . $years->implode(',') . ')' );
        }

        return $query;
    }

    public function scopeInMonths($query, $months, $typeSearch = 'IN')
    {
        if (COUNT($months) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereRaw('month(sau_ph_reports.created_at) IN (' . $months->implode(',') . ')' );

            else if ($typeSearch == 'NOT IN')
                $query->whereRaw('month(sau_ph_reports.created_at) NOT IN (' . $months->implode(',') . ')' );
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

    public function path_base()
    {
        return "reports_images/";
    }

    public function donwload_img($key)
    {
        return Storage::disk($this::DISK)->download("{$this->path_base()}{$this->$key}");
    }

    public function path_image($key)
    {
        if ($this->$key && $this->img_exists($key))
            return Storage::disk($this::DISK)->url("{$this->path_base()}{$this->$key}");
    }

    public function img_exists($key)
    {
        return Storage::disk($this::DISK)->exists("{$this->path_base()}{$this->$key}");
    }

    public function img_delete($key)
    {
        if ($this->$key && $this->img_exists($key))
           Storage::disk($this::DISK)->delete("{$this->path_base()}{$this->$key}");
    }

    public function store_image($key, $fileName, $file)
    {
        Storage::disk($this::DISK)->put("{$this->path_base()}{$fileName}", $file, 'public');
        $this->update([$key => $fileName]);
    }
}
