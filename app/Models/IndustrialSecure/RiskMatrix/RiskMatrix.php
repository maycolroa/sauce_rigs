<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class RiskMatrix extends Model
{
    protected $table = 'sau_rm_risks_matrix';

    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_area_id',
        'employee_process_id',
        'participants'
    ];
     
    public function subprocesses()
    {
        return $this->hasMany(RiskMatrixSubProcess::class, 'risk_matrix_id');
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

    public function macroprocess()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\TagsProcess', 'macroprocess_id');
    }

    /*public function histories()
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
                $query->whereIn('sau_rm_risks_matrix.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_risks_matrix.employee_regional_id', $regionals);
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
                $query->whereIn('sau_rm_risks_matrix.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_risks_matrix.employee_headquarter_id', $ids);
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
                $query->whereIn('sau_rm_risks_matrix.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_risks_matrix.employee_area_id', $areas);
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
                $query->whereIn('sau_rm_risks_matrix.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_risks_matrix.employee_process_id', $processes);
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
                $query->whereIn('sau_rm_risks_matrix.id', $matrix);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_risks_matrix.id', $matrix);
        }

        return $query;
    }
}
