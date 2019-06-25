<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use App\Models\Administrative\Employees\Employee;

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
        'exposedPopulationCuap',
        'exposedPopulationaudiologicalCondition'
    ];

    const GROUPING_COLUMNS = [
        ['sau_employees_regionals', 'employee_regional_id'],
        ['sau_employees_headquarters', 'employee_headquarter_id'],
        ['sau_employees_areas', 'employee_area_id'],
        ['sau_employees_processes', 'employee_process_id'],
        ['sau_employees', 'deal'],
        ['sau_employees_positions', 'employee_position_id']
    ];

    const TYPE_PERCENTAGE = [
        'total' => [],
        'percentage_x_category' => []
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $regionals;
    protected $headquarters;
    protected $areas;
    protected $processes;
    protected $deals;
    protected $positions;
    protected $years;
    protected $dateRange;
    protected $filtersType;
    protected $totalEmployee;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($regionals = [], $headquarters = [], $areas = [], $processes = [], $deals = [], $positions = [], $years = [], $dateRange = [], $filtersType = [])
    {
        $this->regionals = $regionals;
        $this->headquarters = $headquarters;
        $this->areas = $areas;
        $this->processes = $processes;
        $this->deals = $deals;
        $this->positions = $positions;
        $this->years = $years;
        $this->dateRange = $dateRange;
        $this->filtersType = $filtersType;
        $this->totalEmployee = $this->getTotalEmployee();
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
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
        ->inAreas($this->areas, $this->filtersType['areas'])
        ->inProcesses($this->processes, $this->filtersType['processes'])
        ->inDeals($this->deals, $this->filtersType['deals'])
        ->inPositions($this->positions, $this->filtersType['positions'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->where($column, '<>', '')
        ->where('sau_bm_audiometries.base_type', 'Base')
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
        $exposedPopulation = Audiometry::join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id');

        if ($table != 'sau_employees')
        {
            $exposedPopulation->selectRaw($table.".name as name,
                COUNT(sau_employees.id) as count
            ")
            ->join($table, $table.'.id','sau_employees.'.$column)
            ->groupBy($table.'.name');
        }
        else
        {
            $exposedPopulation->selectRaw($table.".".$column." as name,
                COUNT(sau_employees.id) as count
            ")
            ->groupBy($table.'.'.$column);
        }

        $exposedPopulation = $exposedPopulation
            ->inRegionals($this->regionals, $this->filtersType['regionals'])
            ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
            ->inAreas($this->areas, $this->filtersType['areas'])
            ->inProcesses($this->processes, $this->filtersType['processes'])
            ->inDeals($this->deals, $this->filtersType['deals'])
            ->inPositions($this->positions, $this->filtersType['positions'])
            ->where('sau_bm_audiometries.base_type', 'Base')
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
        $informData = [];

        foreach ($columns as $column) {
            $informData[$column[1]] = $this::TYPE_PERCENTAGE;
            $result_total = $this->exposedPopulationForState($column[0], $column[1], $state);
            
            $informData[$column[1]]['total'] = $this->addPercentageEmployee($result_total);
            $informData[$column[1]]['percentage_x_category'] = $this->buildDataChart($result_total);
        }

        return $informData;
    }

    private function addPercentageEmployee($data)
    {
        $result = [];

        if (COUNT($data) > 0)
        {
            foreach ($data as $key => $value)
            {
                array_push($result,[
                    $key, $value, round( ($value / $this->totalEmployee) * 100, 1)
                ]);
            }
        }

        return $result;
    }

    private function exposedPopulationForState($table, $column, $state)
    {
        $audiometryState = Audiometry::join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id');

        if ($table != 'sau_employees')
        {
            $audiometryState->selectRaw($table.".name as name,
                COUNT(sau_employees.id) as count
            ")
            ->join($table, $table.'.id','sau_employees.'.$column)
            ->groupBy($table.'.name');
        }
        else
        {
            $audiometryState->selectRaw($table.".".$column." as name,
                COUNT(sau_employees.id) as count
            ")
            ->groupBy($table.'.'.$column);
        }

        $audiometryState = $audiometryState
            ->inRegionals($this->regionals, $this->filtersType['regionals'])
            ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
            ->inAreas($this->areas, $this->filtersType['areas'])
            ->inProcesses($this->processes, $this->filtersType['processes'])
            ->inDeals($this->deals, $this->filtersType['deals'])
            ->inPositions($this->positions, $this->filtersType['positions'])
            ->inYears($this->years, $this->filtersType['years'])
            ->betweenDate($this->dateRange)
            ->where('sau_bm_audiometries.base_state', '=', $state)
            ->where('sau_bm_audiometries.base_type', 'Base')
            ->pluck('count', 'name');

        return $audiometryState;
    }
    
    /**
     * Returns the reports of the exposed population audiological condition
     * @return collection
     */
    public function exposedPopulationaudiologicalCondition()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = [];

        foreach ($columns as $column) {
            //$informData[$column[1]] = $this::TYPE_PERCENTAGE;
            $result_total = $this->exposedPopulationaudiologicalConditionForColumns($column[0], $column[1]);
            $informData[$column[1]] = $this->prepareDataexposedPopulationaudiologicalCondition($result_total);
        }
    
        return $informData;
    }

    private function prepareDataexposedPopulationaudiologicalCondition($data)
    {
        $result = [];

        $normal = $data->filter(function ($value, $key) {
            return $value->serie == 'Normal';
        });
        
        $total = [];
        $percentage_x_category = [];

        foreach ($normal as $key => $value)
        {
            $percentage_x_category[$value->name] = $value->count;
            array_push($total, [
                $value->name, $value->count, round( ($value->count/ $this->totalEmployee) * 100, 1).'%'
            ]);
        }

        $result["normal"] = [
            'total' => $total,
            'percentage_x_category' => $this->buildDataChart($percentage_x_category)
        ];

        $alterada = $data->filter(function ($value, $key) {
            return $value->serie == 'Alterada';
        });

        $total = [];
        $percentage_x_category = [];

        foreach ($alterada as $key => $value)
        {
            $percentage_x_category[$value->name] = $value->count;
            array_push($total, [
                $value->name, $value->count, round( ($value->count/ $this->totalEmployee) * 100, 1).'%'
            ]);
        }

        $result["alterada"] = [
            'total' => $total,
            'percentage_x_category' => $this->buildDataChart($percentage_x_category)
        ];

        return $result;
    }
    
    private function exposedPopulationaudiologicalConditionForColumns($table, $column)
    {
        $audiometryCoditions = Audiometry::join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id');

        if ($table != 'sau_employees')
        {
            $audiometryCoditions->selectRaw($table.".name as name,
                COUNT(sau_employees.id) as count,
                IF(
                    severity_grade_air_left_4000='Audición normal' AND
                    severity_grade_air_left_6000='Audición normal' AND
                    severity_grade_air_left_8000='Audición normal' AND
                    severity_grade_air_right_4000='Audición normal' AND
                    severity_grade_air_right_6000='Audición normal' AND
                    severity_grade_air_right_8000='Audición normal'
                ,'Normal', 'Alterada') as 'serie'
            ")
            ->join($table, $table.'.id','sau_employees.'.$column)
            ->groupBy($table.'.name', 'serie');
        }
        else
        {
            $audiometryCoditions->selectRaw($table.".".$column." as name,
                COUNT(sau_employees.id) as count,
                IF(
                    severity_grade_air_left_4000='Audición normal' AND
                    severity_grade_air_left_6000='Audición normal' AND
                    severity_grade_air_left_8000='Audición normal' AND
                    severity_grade_air_right_4000='Audición normal' AND
                    severity_grade_air_right_6000='Audición normal' AND
                    severity_grade_air_right_8000='Audición normal'
                ,'Normal', 'Alterada') as 'serie'
            ")
            ->groupBy($table.'.'.$column, 'serie');
        }

        $audiometryCoditions = $audiometryCoditions
            ->inRegionals($this->regionals, $this->filtersType['regionals'])
            ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
            ->inAreas($this->areas, $this->filtersType['areas'])
            ->inProcesses($this->processes, $this->filtersType['processes'])
            ->inDeals($this->deals, $this->filtersType['deals'])
            ->inPositions($this->positions, $this->filtersType['positions'])
            ->inYears($this->years, $this->filtersType['years'])
            ->betweenDate($this->dateRange)
            ->where('sau_bm_audiometries.base_type', 'Base')
            ->get();

        return $audiometryCoditions;
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

    /**
     * Return the total amount of exposed population
     *
     * @return void
     */
    private function getTotalEmployee()
    {
        $exposedPopulation = Audiometry::join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
            ->inRegionals($this->regionals, $this->filtersType['regionals'])
            ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
            ->inAreas($this->areas, $this->filtersType['areas'])
            ->inProcesses($this->processes, $this->filtersType['processes'])
            ->inDeals($this->deals, $this->filtersType['deals'])
            ->inPositions($this->positions, $this->filtersType['positions'])
            ->where('sau_bm_audiometries.base_type', 'Base')
            ->count();

        return $exposedPopulation;
    }
}