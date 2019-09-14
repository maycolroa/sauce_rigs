<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;

class InformManagerMusculoskeletalAnalysis
{
    /**
     * defines the availables informs
     *
     * IMPORTANT NOTE:
     * THERE MUST EXIST A METHOD THAT RETURNS THE INFORM DATA
     * WITH THE SAME EXACT NAME FOR EACH NAME WITHIN THIS ARRAY
     * 
     * @var array
     */
    const INFORMS = [
        'cardiovascularRisk',
        'osteomuscularGroup',
        'imcClassification',
        'abdominalPerimeterClassification',
        'consolidatedRiskCriterion',
        'prioritizationMedicalCriteria',
        'exerciseHabit',
        'liquorHabit',
        'cigaretteHabit'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $consolidatedPersonalRiskCriterion;
    protected $branchOffice;
    protected $companies;
    protected $dateRange;
    protected $filtersType;

    /**
     * create an instance and set the attribute class
     * @param array $consolidatedPersonalRiskCriterion
     */
    function __construct($consolidatedPersonalRiskCriterion = [], $branchOffice = [], $companies = [], $dateRange = [], $filtersType = [])
    {
        $this->consolidatedPersonalRiskCriterion = $consolidatedPersonalRiskCriterion;
        $this->branchOffice = $branchOffice;
        $this->companies = $companies;
        $this->dateRange = $dateRange;
        $this->filtersType = $filtersType;
    }

    /**
     * returns the data for the informs in the view according
     * to the $components parameter
     *
     * if $components is not defined, returns data for all the informs
     * 
     * @param  array $components
     * @return collection
     */
    public function getInformData($components = [])
    {
        if (!$components) {
            $components = $this::INFORMS;
        }
        $informData = collect([]);
        foreach ($components as $component) {
            $informData->put($component, $this->$component());
        }
        return $informData->toArray();
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function cardiovascularRisk()
    {
        return $this->getReportPerColumn('cardiovascular_risk');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function osteomuscularGroup()
    {
        return $this->getReportPerColumn('osteomuscular_group');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function imcClassification()
    {
        return $this->getReportPerColumn('imc_lassification');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function abdominalPerimeterClassification()
    {
        return $this->getReportPerColumn('abdominal_perimeter_classification');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function consolidatedRiskCriterion()
    {
        return $this->getReportPerColumn('consolidated_personal_risk_criterion');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function prioritizationMedicalCriteria()
    {
        return $this->getReportPerColumn('prioritization_medical_criteria');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function exerciseHabit()
    {
        return $this->getReportPerColumn('exercise_habit');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function liquorHabit()
    {
        return $this->getReportPerColumn('liquor_habit');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function cigaretteHabit()
    {
        return $this->getReportPerColumn('cigarette_habit');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function getReportPerColumn($column)
    {
        $data = MusculoskeletalAnalysis::selectRaw(
            $column." AS ".$column.",
            COUNT(patient_identification) AS count
        ")
        ->inConsolidatedPersonalRiskCriterion($this->consolidatedPersonalRiskCriterion, $this->filtersType['consolidatedPersonalRiskCriterion'])
        ->inBranchOffice($this->branchOffice, $this->filtersType['branchOffice'])
        ->inCompanies($this->companies, $this->filtersType['companies'])
        ->betweenDate($this->dateRange)
        ->where($column, '<>', '')
        ->groupBy($column)
        ->pluck('count', $column);

        return $this->buildDataChart($data);
    }

    /**
     * takes the raw data collection and builds
     * a new collection with the right structure for the
     * pie chart data
     * @param  collection $rawData
     * @return collection
     */
    protected function buildDataChart($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $label => $count) {
            array_push($labels, $label);
            array_push($data, ['name' => $label, 'value' => $count]);
            $total += $count;
        }

        return collect([
            'labels' => $labels,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
    }
}