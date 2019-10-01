<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\Tracing;

class InformIndividualManagerMusculoskeletalAnalysis
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
        $data = MusculoskeletalAnalysis::select('sau_bm_musculoskeletal_analysis.*')
        ->where('patient_identification', $this->id)
        ->get();

        return $data;
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function oldTracings()
    {
        $tracings = Tracing::where('identification', $this->id)->get();

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