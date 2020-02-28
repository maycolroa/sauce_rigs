<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use DB;

class InformManagerRespiratoryAnalysis
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
        'breathingProblems',
        'classificationAts',
        'classificationObstructive',
        'classificationRestrictive',
        'breathingProblemsRegional'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $regional;
    protected $deal;
    protected $interpretation;
    protected $dateRange;
    protected $filtersType;

    /**
     * create an instance and set the attribute class
     * @param array $regional
     */
    function __construct($regional = [], $deal = [], $interpretation = [], $dateRange = [], $filtersType = [])
    {
        $this->regional = $regional;
        $this->deal = $deal;
        $this->interpretation = $interpretation;
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
    private function breathingProblems()
    {
        return $this->getReportPerColumn('breathing_problems');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function classificationAts()
    {
        return $this->getReportPerColumn('classification_according_to_ats');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function classificationObstructive()
    {
        return $this->getReportPerColumn('ats_obstruction_classification', ['classification_according_to_ats' => 'Obstructiva']);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function classificationRestrictive()
    {
        return $this->getReportPerColumn('ats_restrictive_classification', ['classification_according_to_ats' => 'Restrictiva']);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function breathingProblemsRegional()
    {
        return $this->getReportPerColumn('regional', ['breathing_problems' => 'SI']);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function getReportPerColumn($column, $condition = null)
    {
        $data = RespiratoryAnalysis::selectRaw(
            $column." AS ".$column.",
            COUNT(patient_identification) AS count
        ")
        ->inRegional($this->regional, $this->filtersType['regional'])
        ->inDeal($this->deal, $this->filtersType['deal'])
        ->inInterpretation($this->interpretation, $this->filtersType['interpretation'])
        ->betweenDate($this->dateRange)
        ->where($column, '<>', '')
        ->whereNotNull($column)
        ->groupBy($column);

        if ($condition)
            $data->where($condition);

        return $this->buildDataChart($data->pluck('count', $column));
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