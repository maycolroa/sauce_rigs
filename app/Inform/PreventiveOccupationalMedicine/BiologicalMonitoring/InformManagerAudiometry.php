<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use App\Administrative\Employee;

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
        'airRightPtaPie',
        'exposedPopulation',
        'exposedPopulationCuat',
        'exposedPopulationCuap'
    ];

    const GROUPING_COLUMNS = [
        ['sau_employees_regionals', 'employee_regional_id'],
        ['sau_employees_headquarters', 'employee_headquarter_id'],
        ['sau_employees_areas', 'employee_area_id'],
        ['sau_employees_processes', 'employee_process_id'],
        ['sau_employees_businesses', 'employee_business_id'],
        ['sau_employees_positions', 'employee_position_id']
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
    protected $positions;
    protected $years;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($regionals = [], $headquarters = [], $areas = [], $processes = [], $businesses = [], $positions = [], $years = [])
    {
        $this->regionals = $regionals;
        $this->headquarters = $headquarters;
        $this->areas = $areas;
        $this->processes = $processes;
        $this->businesses = $businesses;
        $this->positions = $positions;
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
     * Returns the reports of left air pta.
     * @return collection
     */
    public function airLeftPtaPie()
    {
        return $this->airPtaPie('severity_grade_air_left_pta');
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function airRightPtaPie()
    {
        return $this->airPtaPie('severity_grade_air_right_pta');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function airPtaPie($column)
    {
        $airPtaPie = Audiometry::selectRaw(
            $column." as ".$column.",
            COUNT(".$column.") as count
        ")
        ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        ->inRegionals($this->regionals)
        ->inHeadquarters($this->headquarters)
        ->inAreas($this->areas)
        ->inProcesses($this->processes)
        ->inBusinesses($this->businesses)
        ->inPositions($this->positions)
        ->inYears($this->years)
        ->where($column, '<>', '')
        ->groupBy($column)
        ->pluck('count', $column);

        return $this->buildDataChart($airPtaPie);
    }

    /**
     * Returns the exposed population reports grouped by each filter type
     * @return collection
     */
    public function exposedPopulation()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->exposedPopulationForColumn($column[0], $column[1]));
        }
    
        return $informData->toArray();
    }

    /**
     * Returns the exposed population reports according to the column sent by parameter.
     *
     * @param String $table
     * @param String $column
     * @return collection
     */
    public function exposedPopulationForColumn($table, $column)
    {
        $exposedPopulation = Employee::selectRaw($table.".name as name,
            COUNT(sau_employees.id) as count
        ")
        ->join($table, $table.'.id','sau_employees.'.$column)
        ->inRegionals($this->regionals)
        ->inHeadquarters($this->headquarters)
        ->inAreas($this->areas)
        ->inProcesses($this->processes)
        ->inBusinesses($this->businesses)
        ->inPositions($this->positions)
        ->groupBy($column)
        ->pluck('count', 'name');

        return $this->buildDataChart($exposedPopulation);
    }

    /**
     * Returns the reports of the exposed population cuat.
     * @return collection
     */
    public function exposedPopulationCuat()
    {
        return $this->exposedPopulationState('CUAT');
    }

    /**
     * Returns the reports of the exposed population cuap.
     * @return collection
     */
    public function exposedPopulationCuap()
    {
        return $this->exposedPopulationState('CUAP');
    }

    private function exposedPopulationState($state)
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->exposedPopulationForState($column[0], $column[1], $state));
        }
    
        return $informData->toArray();
    }

    private function exposedPopulationForState($table, $column, $state)
    {
        $audiometryState = Audiometry::selectRaw($table.".name as name,
            COUNT(sau_employees.id) as count
        ")
        ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        ->join($table, $table.'.id','sau_employees.'.$column)
        ->inRegionals($this->regionals)
        ->inHeadquarters($this->headquarters)
        ->inAreas($this->areas)
        ->inProcesses($this->processes)
        ->inBusinesses($this->businesses)
        ->inPositions($this->positions)
        ->inYears($this->years)
        ->where('sau_bm_audiometries.base_state', '=', $state)
        ->groupBy($column)
        ->pluck('count', 'name');

        return $this->buildDataChart($audiometryState);
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