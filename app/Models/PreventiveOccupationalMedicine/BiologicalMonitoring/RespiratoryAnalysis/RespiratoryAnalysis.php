<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\CompanyTrait;

class RespiratoryAnalysis extends Model
{
    use CompanyTrait;

    protected $table = 'sau_bm_respiratory_analysis';

    protected $fillable = [
        'company_id',
        'patient_identification',
        'name',
        'sex',
        'deal',
        'regional',
        'date_of_birth',
        'age',
        'income_date',
        'antiquity',
        'area',
        'position',
        'habits',
        'history_of_respiratory_pathologies',
        'measurement_date',
        'mg_m3_concentration',
        'ir',
        'type_of_exam',
        'year_of_spirometry',
        'spirometry',
        'date_of_realization',
        'symptomatology',
        'cvf_average_percentage',
        'vef1_average_percentage',
        'vef1_cvf_average',
        'fef_25_75_porcentage',
        'interpretation',
        'type_of_exam_2',
        'date_of_realization_2',
        'rx_oit',
        'quality',
        'yes_1',
        'not_1',
        'answer_yes_describe',
        'yes_2',
        'not_2',
        'answer_yes_describe_2',
        'other_abnormalities',
        'fully_negative',
        'observation',        
        'breathing_problems',
        'classification_according_to_ats',
        'ats_obstruction_classification',
        'ats_restrictive_classification',
        'state'
    ];

    protected $dates = [
    ];

    /**
     * filters checks through the given interpretation
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $interpretation
     * @return Illuminate\Database\Eloquent\Builder
     */
   public function scopeInInterpretation($query, $interpretation, $typeSearch = 'IN')
    {
        if (COUNT($interpretation) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_respiratory_analysis.interpretation', $interpretation);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_respiratory_analysis.interpretation', $interpretation);
        }

        return $query;
    }

    /**
     * filters checks through the given regional
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $regional
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRegional($query, $regional, $typeSearch = 'IN')
    {
        if (COUNT($regional) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_respiratory_analysis.regional', $regional);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_respiratory_analysis.regional', $regional);
        }

        return $query;
    }

    /**
     * filters checks through the given deal
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $deal
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDeal($query, $deal, $typeSearch = 'IN')
    {
        if (COUNT($deal) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_respiratory_analysis.deal', $deal);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_respiratory_analysis.deal', $deal);
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
            $query->whereBetween('sau_bm_respiratory_analysis.date_of_realization', $dates);
            
            return $query;
        }
    }
}