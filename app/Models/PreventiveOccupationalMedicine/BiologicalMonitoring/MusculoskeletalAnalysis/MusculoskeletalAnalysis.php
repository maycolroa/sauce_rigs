<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\CompanyTrait;

class MusculoskeletalAnalysis extends Model
{
    use CompanyTrait;

    protected $table = 'sau_bm_musculoskeletal_analysis';

    protected $fillable = [
        'company_id',
        'patient_identification',
        'name',
        'date',
        'evaluation_type',
        'evaluation_format',
        'company',
        'branch_office',
        'sex',
        'age',
        'phone',
        'phone_alternative',
        'eps',
        'afp',
        'position',
        'antiquity',
        'state',
        'ant_atep_ep',
        'which_ant_atep_ep',
        'exercise_habit',
        'exercise_frequency',
        'liquor_habit',
        'liquor_frequency',
        'exbebedor_habit',
        'liquor_suspension_time',
        'cigarette_habit',
        'cigarette_frequency',
        'habit_extra_smoker',
        'cigarrillo_suspension_time',
        'activity_extra_labor',
        'weight',
        'size',
        'imc',
        'imc_lassification',
        'abdominal_perimeter',
        'abdominal_perimeter_classification',
        'diagnostic_code_1',
        'diagnostic_1',
        'diagnostic_code_2',
        'diagnostic_2',
        'diagnostic_code_3',
        'diagnostic_3',
        'diagnostic_code_4',
        'diagnostic_4',
        'diagnostic_code_5',
        'diagnostic_5',
        'diagnostic_code_6',
        'diagnostic_6',
        'diagnostic_code_7',
        'diagnostic_7',
        'diagnostic_code_8',
        'diagnostic_8',
        'diagnostic_code_9',
        'diagnostic_9',
        'diagnostic_code_10',
        'diagnostic_10',
        'diagnostic_code_11',
        'diagnostic_11',
        'diagnostic_code_12',
        'diagnostic_12',
        'diagnostic_code_13',
        'diagnostic_13',
        'cardiovascular_risk',
        'osteomuscular_classification',
        'osteomuscular_group',
        'age_risk',
        'pathological_background_risks',
        'extra_labor_activities_risk',
        'sedentary_risk',
        'imc_risk',
        'consolidated_personal_risk_punctuation',
        'consolidated_personal_risk_criterion',
        'prioritization_medical_criteria',
        'concept',
        'recommendations',
        'observations',
        'restrictions',
        'remission',
        'description_medical_exam',
        'symptom',
        'symptom_type',
        'body_part',
        'periodicity',
        'workday',
        'symptomatology_observations',
        'id3'
    ];

    protected $dates = [
    ];

    /**
     * filters checks through the given consolidatedPersonalRiskCriterion
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $consolidatedPersonalRiskCriterion
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInConsolidatedPersonalRiskCriterion($query, $consolidatedPersonalRiskCriterion, $typeSearch = 'IN')
    {
        if (COUNT($consolidatedPersonalRiskCriterion) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_musculoskeletal_analysis.consolidated_personal_risk_criterion', $consolidatedPersonalRiskCriterion);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_musculoskeletal_analysis.consolidated_personal_risk_criterion', $consolidatedPersonalRiskCriterion);
        }

        return $query;
    }

    /**
     * filters checks through the given branchOffice
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $branchOffice
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInBranchOffice($query, $branchOffice, $typeSearch = 'IN')
    {
        if (COUNT($branchOffice) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_musculoskeletal_analysis.branch_office', $branchOffice);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_musculoskeletal_analysis.branch_office', $branchOffice);
        }

        return $query;
    }

    /**
     * filters checks through the given company
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $company
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCompanies($query, $company, $typeSearch = 'IN')
    {
        if (COUNT($company) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_bm_musculoskeletal_analysis.company', $company);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_bm_musculoskeletal_analysis.company', $company);
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
            $query->whereBetween('sau_bm_musculoskeletal_analysis.date', $dates);
            return $query;
        }
    }
}