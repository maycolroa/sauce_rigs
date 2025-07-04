<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\TracingRespiratoryAnalysis;

class InformIndividualManagerRespiratoryAnalysis
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
        'dataAnalysis',
        'oldTracings'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $id;

    /**
     * create an instance and set the attribute class
     * @param array $id
     */
    function __construct($id)
    {
        $this->id = $id;
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
    private function dataAnalysis()
    {
        $data = RespiratoryAnalysis::select('sau_bm_respiratory_analysis.*')
        ->where('patient_identification', $this->id)
        ->orderBy('year_of_spirometry')
        ->get();

        foreach ($data as $key => $value) 
        {
            if ($value->employee_regional_id)
                $value->employee_regional_id = $value->regionalEmployee->name;
                
            if ($value->employee_headquarter_id)
                $value->employee_headquarter_id = $value->headquarterEmployee->name;

            if ($value->employee_process_id)
                $value->employee_process_id = $value->processEmployee->name;
                
            if ($value->employee_area_id)
                $value->employee_area_id = $value->areaEmployee->name;
        }

        return $data;
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function oldTracings()
    {
        $tracings = TracingRespiratoryAnalysis::where('identification', $this->id)->get();

        $oldTracings = [];

        foreach ($tracings as $tracing)
        {
            array_push($oldTracings, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy->name,
                'updated_at' => $tracing->updated_at->toDateString()
            ]);
        }

        return $oldTracings;
    }
}