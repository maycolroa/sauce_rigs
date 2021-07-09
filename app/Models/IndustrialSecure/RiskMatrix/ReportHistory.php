<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{

    protected $table = 'sau_rm_report_histories';

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
        'risk'
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
                $query->whereIn('sau_rm_report_histories.regional', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.regional', $regionals);
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
                $query->whereIn('sau_rm_report_histories.headquarter', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.headquarter', $ids);
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
                $query->whereIn('sau_rm_report_histories.area', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.area', $areas);
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
                $query->whereIn('sau_rm_report_histories.process', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.process', $processes);
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
        if (COUNT($macroprocesses) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rm_report_histories.macroprocess', $macroprocesses);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.macroprocess', $macroprocesses);
        }

        return $query;
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRisks($query, $risks, $typeSearch = 'IN')
    {
        if (COUNT($risks) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rm_report_histories.risk', $risks);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rm_report_histories.risk', $risks);
        }

        return $query;
    }
}
