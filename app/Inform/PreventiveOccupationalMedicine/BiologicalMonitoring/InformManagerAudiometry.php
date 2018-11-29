<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;

class InformManagerAudiometry
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
        'airLeftPtaPie',
        'airRightPtaPie'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $regionals;
    protected $headquarters;
    protected $areas;
    protected $processes;
    protected $businesses;
    protected $years;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($regionals = [], $headquarters = [], $areas = [], $processes = [], $businesses = [], $years = [])
    {
        $this->regionals = $regionals;
        $this->headquarters = $headquarters;
        $this->areas = $areas;
        $this->processes = $processes;
        $this->businesses = $businesses;
        $this->years = $years;
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
     * return the origin disease reports pie data for the view
     * @return collection
     */
    public function airLeftPtaPie()
    {
        $airLeftPtaPie = Audiometry::selectRaw("
            severity_grade_air_left_pta as severity_grade_air_left_pta,
            COUNT(severity_grade_air_left_pta) as count
        ")
        ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        ->inRegionals($this->regionals)
        ->inHeadquarters($this->headquarters)
        ->inAreas($this->areas)
        ->inProcesses($this->processes)
        ->inBusinesses($this->businesses)
        ->inYears($this->years)
        ->where('severity_grade_air_left_pta', '<>', '')
        ->groupBy('severity_grade_air_left_pta')
        ->pluck('count', 'severity_grade_air_left_pta');

        return $this->buildDataForPieChart($airLeftPtaPie);
    }

    /**
     * return the origin disease reports pie data for the view
     * @return collection
     */
    public function airRightPtaPie()
    {
        $airRightPtaPie = Audiometry::selectRaw("
            severity_grade_air_right_pta as severity_grade_air_right_pta,
            COUNT(severity_grade_air_right_pta) as count
        ")
        ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        ->inRegionals($this->regionals)
        ->inHeadquarters($this->headquarters)
        ->inAreas($this->areas)
        ->inProcesses($this->processes)
        ->inBusinesses($this->businesses)
        ->inYears($this->years)
        ->where('severity_grade_air_right_pta', '<>', '')
        ->groupBy('severity_grade_air_right_pta')
        ->pluck('count', 'severity_grade_air_right_pta');

        return $this->buildDataForPieChart($airRightPtaPie);
    }

    /**
     * takes the raw data collection and builds
     * a new collection with the right structure for the
     * pie chart data
     * @param  collection $rawData
     * @return collection
     */
    protected function buildDataForPieChart($rawData)
    {
        $labels = [];
        $data = [];
        foreach ($rawData as $label => $count) {
            array_push($labels, $label);
            array_push($data, ['name' => $label, 'value' => $count]);
        }

        return collect([
            'labels' => $labels,
            'datasets' => [
                'data' => $data,
            ]
        ]);
    }
}