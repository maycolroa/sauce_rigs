<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class HistoryQualificationChange extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_dm_history_qualification_change';

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'user_id',
        'danger_matrix_id',
        'activity_id',
        'danger_id',
        'activity_danger_id',
        'qualification_old',
        'qualification_new',
    ];

    public function danger()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Dangers\Danger', 'danger_id');
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDangers($query, $dangers, $typeSearch = 'IN')
    {
        if (COUNT($dangers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_history_qualification_change.danger_id', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_history_qualification_change.danger_id', $dangers);
        }

        return $query;
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInActivities($query, $activities, $typeSearch = 'IN')
    {
        if (COUNT($activities) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_history_qualification_change.activity_id', $activities);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_history_qualification_change.activity_id', $activities);
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
                $query->whereIn('sau_dangers_matrix.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.employee_regional_id', $regionals);
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
                $query->whereIn('sau_dangers_matrix.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.employee_headquarter_id', $ids);
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
                $query->whereIn('sau_dangers_matrix.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.employee_area_id', $areas);
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
                $query->whereIn('sau_dangers_matrix.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.employee_process_id', $processes);
        }

        return $query;
    }

    public function scopeInYears($query, $year, $typeSearch = 'IN')
    {
        if (COUNT($year) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dangers_matrix.year', $year);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.year', $year);
        }

        return $query;
    }
}
