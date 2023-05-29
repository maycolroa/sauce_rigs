<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ReportHistory extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dm_report_histories';

    protected $fillable = [
        'company_id',
        'year',
        'month',
        'regional',
        'area',
        'headquarter',
        'process',
        'macroprocess',
        'qualification',
        'type_configuration',
        'danger',
        'danger_description'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
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
                $query->whereIn('sau_dm_report_histories.regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_report_histories.regional_id', $regionals);
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
                $query->whereIn('sau_dm_report_histories.headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_report_histories.headquarter_id', $ids);
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
                $query->whereIn('sau_dm_report_histories.area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_report_histories.area_id', $areas);
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
                $query->whereIn('sau_dm_report_histories.process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_report_histories.process_id', $processes);
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
                $query->where('sau_dm_report_histories.macroprocess', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_dm_report_histories.macroprocess', 'NOT REGEXP', $regexp);
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
                $query->whereIn('sau_dm_report_histories.danger', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_report_histories.danger', $dangers);
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
                $query->where('sau_dm_report_histories.danger_description', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_dm_report_histories.danger_description', 'NOT REGEXP', $regexp);
        }

        return $query;
    }

    public function regional2()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'regional_id');
    }

    public function headquarter2()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'headquarter_id');
    }

    public function area2()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'area_id');
    }

    public function process2()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'process_id');
    }
}
