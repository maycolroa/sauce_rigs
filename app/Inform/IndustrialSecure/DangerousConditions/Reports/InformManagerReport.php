<?php

namespace App\Inform\IndustrialSecure\DangerousConditions\Reports;

use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Users\User;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use DB;
use App\Traits\UtilsTrait;
use Illuminate\Support\Facades\Auth;

class InformManagerReport
{
    use UtilsTrait;

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
        'report_per_headquarter',
        'report_per_area',
        'report_per_user',
        'report_per_headquarter_area',
        'reports'
    ];

    const GROUPING_COLUMNS = [
        ['sau_employees_processes.name', 'process'],
        ['sau_employees_regionals.name', 'regional'],        
        ['sau_employees_headquarters.name', 'headquarter'],
        ['sau_employees_areas.name', 'area'],
        ['sau_ph_conditions.description', 'condition'], 
        ['sau_ph_reports.rate', 'rate'],
        ['sau_users.name', 'user']
    ];

    protected $company;
    protected $regionals;
    protected $headquarters;
    protected $processes;
    protected $areas;
    protected $conditions;
    protected $rates;
    protected $users;
    protected $years;
    protected $months;
    protected $dates;
    protected $filtersType;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($company = '', $regionals = '', $headquarters = '', $processes = '', $areas = '', $conditions = '', $rates = '', $users = '', $years = '', $months = '', $dates = '', $filtersType = '')
    {
        $this->company = $company;
        $this->regionals = $regionals;
        $this->headquarters = $headquarters;
        $this->processes = $processes;
        $this->areas = $areas;
        $this->conditions = $conditions;
        $this->rates = $rates;
        $this->users = $users;
        $this->years = $years;
        $this->months = $months;
        $this->dates = $dates;
        $this->filtersType = $filtersType;


        $this->regionalsFilter = [];
        $this->headquartersFilter = [];
        $this->processesFilter = [];
        $this->areasFilter = [];
        $this->locationLevelForm = '';

        try
        {
            $this->configLevel = ConfigurationsCompany::company($this->company)->findByKey('filter_inspections');
            
        } catch (\Exception $e) {
            $this->configLevel = 'NO';
        }

        if ($this->configLevel == 'SI')
        {
            $this->locationLevelForm = ConfigurationsCompany::company($this->company)->findByKey('location_level_form_user_inspection_filter');

            if ($this->locationLevelForm)
            {
                $id = Auth::user() ? Auth::user()->id : (isset($builder->user) ? $builder->user : null);

                if ($id)
                {
                    if ($this->locationLevelForm == 'Regional')
                    {
                        $this->regionalsFilter = User::find($id)->regionals()->pluck('id');
                    }
                    else if ($this->locationLevelForm == 'Sede')
                    {
                        $this->regionalsFilter = User::find($id)->regionals()->pluck('id');
                        $this->headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                    }
                    else if ($this->locationLevelForm == 'Proceso')
                    {
                        $this->regionalsFilter = User::find($id)->regionals()->pluck('id');
                        $this->headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                        $this->processesFilter = User::find($id)->processes()->pluck('id');
                    }
                    else if ($this->locationLevelForm == 'Área')
                    {
                        $this->regionalsFilter = User::find($id)->regionals()->pluck('id');
                        $this->headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                        $this->processesFilter = User::find($id)->processes()->pluck('id');
                        $this->areasFilter = User::find($id)->areas()->pluck('id');
                    }
                }
            }
        }
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
    private function report_per_headquarter()
    {
        return $this->getReportPerColumn('sau_employees_headquarters.name');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function report_per_area()
    {
        return $this->getReportPerColumn('sau_employees_areas.name');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function report_per_user()
    {
        return $this->getReportPerColumn('sau_users.name');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function getReportPerColumn($column)
    {
        $data = Report::selectRaw(
            $column." AS name,
            COUNT(sau_ph_reports.id) AS count
        ")
        ->join('sau_users', 'sau_users.id', 'sau_ph_reports.user_id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
        ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_reports.employee_area_id')
        ->where($column, '<>', '')
        ->whereIn('sau_employees_headquarters.id', $this->headquartersFilter)
        ->whereIn('sau_employees_areas.id', $this->areasFilter)
        ->withoutGlobalScopes()
        ->where('company_id', $this->company)
        ->groupBy($column)
        ->orderBy($column)
        ->pluck('count', $column);

        return $this->buildDataChart($data);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function report_per_headquarter_area()
    {
        $data = Report::select(
            DB::raw("CONCAT(sau_employees_headquarters.name, ' - ', sau_employees_areas.name) AS name"),
            DB::raw("COUNT(sau_ph_reports.id) AS count")
        )
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
        ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_reports.employee_area_id')
        ->whereIn('sau_employees_headquarters.id', $this->headquartersFilter)
        ->whereIn('sau_employees_areas.id', $this->areasFilter)
        ->groupBy('sau_employees_headquarters.name', 'sau_employees_areas.name')
        ->withoutGlobalScopes()
        ->where('company_id', $this->company)
        ->orderBy('name')
        ->pluck('count', 'name');

        return $this->buildDataChart($data);
    }

    public function reports()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->reportBar($column[0]));
        }
    
        return $informData->toArray();
    }

    private function reportBar($column)
    {
        $consultas = Report::select("$column as category", 
            DB::raw('COUNT(DISTINCT sau_ph_reports.id) AS count'))
        ->withoutGlobalScopes()
        ->join('sau_ph_conditions', 'sau_ph_conditions.id', 'sau_ph_reports.condition_id')
        ->join('sau_ph_conditions_types', 'sau_ph_conditions_types.id', 'sau_ph_conditions.condition_type_id')
        ->join('sau_users', 'sau_users.id', 'sau_ph_reports.user_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_reports.employee_area_id' )
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id','sau_ph_reports.employee_regional_id' )
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id','sau_ph_reports.employee_process_id' )
        ->where('sau_ph_reports.company_id', $this->company)
        ->groupBy('category');

        if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_reports.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->headquartersFilter) > 0)
        {
            $consultas->whereIn('sau_ph_reports.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_reports.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->processesFilter) > 0)
        {
            $consultas->whereIn('sau_ph_reports.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_reports.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_reports.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->areasFilter) > 0)
        {
            $consultas->whereIn('sau_ph_reports.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_reports.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_reports.employee_process_id', $this->processesFilter)->whereIn('sau_ph_reports.employee_area_id', $this->areasFilter);
        }
        
        //\Log::info($consultas->toSql());

        if (COUNT($this->conditions) > 0)
            $consultas->inConditions($this->conditions, $this->filtersType['conditions']);

        if (COUNT($this->rates) > 0)
            $consultas->inRates($this->rates, $this->filtersType['rates']);

        if (COUNT($this->users) > 0)
            $consultas->inUsers($this->users, $this->filtersType['users']); 

        if (COUNT($this->years) > 0)      
            $consultas->inYears($this->years);

        if (COUNT($this->months) > 0)
            $consultas->inMonths($this->months);

        if (COUNT($this->dates) > 0)
            $consultas->betweenDate($this->dates);

        if (COUNT($this->headquarters) > 0)
            $consultas->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->areas) > 0)
            $consultas->inAreas($this->areas, $this->filtersType['areas']);

        if (COUNT($this->regionals) > 0)
            $consultas->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->processes) > 0)
            $consultas->inProcesses($this->processes, $this->filtersType['processes']);

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart2($consultas);
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

     protected function buildDataChart2($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $label => $count) {
            $label2 = strlen($label) > 30 ? substr($this->sanear_string($label), 0, 30).'...' : $label;
            array_push($labels, $label2);
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

    /*public function locationWithCondition($headquarters)
    {
        $report = Report::select(
            DB::raw('count(sau_ph_reports.id) as reports'),
            'sau_ph_conditions.description as condition'
        )
        ->join('sau_ph_conditions', 'sau_ph_conditions.id', 'sau_ph_reports.condition_id')
        ->whereIn('sau_ph_reports.employee_headquarter_id', $headquarters)
        ->groupBy('sau_ph_conditions.description')
        ->orderBy(DB::raw('count(sau_ph_reports.id)'), 'desc')
        ->first();

        if (!$report) {
            return [
                'reports' => 0,
                'condition' => '',
            ];
        }

        return [
            'reports' => $report->reports,
            'condition' => $report->condition,
        ];

    }*/
}