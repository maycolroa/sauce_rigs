<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class DangerMatrix extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dangers_matrix';

    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_area_id',
        'employee_process_id',
        'participants',
        'year'
    ];
     
    public function activities()
    {
        return $this->hasMany(DangerMatrixActivity::class, 'danger_matrix_id');
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

    public function histories()
    {
        return $this->hasMany(ChangeHistory::class, 'danger_matrix_id');
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

    /**
     * filters checks through the given macroprocesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $macroprocesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInMacroprocesses($query, $macroprocesses, $typeSearch = 'IN')
    {
        $regexp = [];

        foreach ($macroprocesses as $key => $value)
        {
            $regexp[] = "((^|,)($value)(,|$))";
        }

        if (COUNT($regexp) > 0)
        {
            $regexp = implode("|", $regexp);

            if ($typeSearch == 'IN')
                $query->where('sau_employees_processes.types', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_employees_processes.types', 'NOT REGEXP', $regexp);
        }

        return $query;
    }

    /**
     * filters checks through the given matrix
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $matrix
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInMatrix($query, $matrix, $typeSearch = 'IN')
    {
        if (COUNT($matrix) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dangers_matrix.id', $matrix);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dangers_matrix.id', $matrix);
        }

        return $query;
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
                $query->whereIn('sau_dm_activity_danger.danger_id', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_activity_danger.danger_id', $dangers);
        }

        return $query;
    }

    /**
     * filters checks through the given macroprocesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $macroprocesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDangerDescription($query, $dangerdescription, $typeSearch = 'IN')
    {
        $regexp = [];

        foreach ($dangerdescription as $key => $value)
        {
            $regexp[] = "((^|,)($value)(,|$))";
        }

        if (COUNT($regexp) > 0)
        {
            $regexp = implode("|", $regexp);

            if ($typeSearch == 'IN')
                $query->where('sau_dm_activity_danger.danger_description', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_dm_activity_danger.danger_description', 'NOT REGEXP', $regexp);
        }

        return $query;
    }

    public function scopeInPositions($query, $positions, $typeSearch = 'IN')
    {
        if (COUNT($positions) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_activity_danger_positions.employee_position_id', $positions);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_activity_danger_positions.employee_position_id', $positions);
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
