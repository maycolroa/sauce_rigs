<?php

namespace App\Inform\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspections;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use DB;
use App\Traits\UtilsTrait;
use App\Models\Administrative\Users\User;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Illuminate\Support\Facades\Auth;

class InformManagerInspections
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
        'inspections',
        'compliance',
        'inspectionsType2',
        'complianceType2'
    ];

    const GROUPING_COLUMNS = [
        ['sau_employees_regionals.name', 'regional'],        
        ['sau_employees_headquarters.name', 'headquarter'],
        ['sau_employees_processes.name', 'process'],
        ['sau_employees_areas.name', 'area'],
        ['sau_ph_inspection_sections.name', 'theme'], 
        ['sau_ph_inspections.name', 'inspection']
    ];

    protected $regionals;
    protected $headquarters;
    protected $processes;
    protected $areas;
    protected $themes;
    protected $filtersType;
    protected $dates;
    protected $company;
    protected $inspections;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($company = '', $regionals = '', $headquarters = '', $processes = '', $areas = '', $themes = '', $filtersType = '', $dates = '', $inspections = '', $qualifiers = '')
    {
        $this->company = $company;
        $this->regionals = $regionals;
        $this->headquarters = $headquarters;
        $this->processes = $processes;
        $this->areas = $areas;
        $this->themes = $themes;
        $this->filtersType = $filtersType;
        $this->dates = $dates;
        $this->inspections = $inspections;
        $this->qualifiers = $qualifiers;

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
    private function inspectionsBar($column)
    {
        $consultas = InspectionItemsQualificationAreaLocation::select("$column as category", 
            DB::raw('COUNT(DISTINCT sau_ph_inspections.id) AS count'))
            ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
            ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
            ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id')
            //->inThemes($this->themes, $this->filtersType['themes'])
            //->inInspections($this->inspections, $this->filtersType['inspections'])
            ->betweenDate($this->dates)
            ->withoutGlobalScopes()
            ->where('sau_ph_inspections.company_id', $this->company)
            ->where('sau_ph_inspections.type_id', 1)
            ->groupBy('category', 'sau_ph_inspection_items_qualification_area_location.qualification_date');

        if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $this->areasFilter);
        }

        if (COUNT($this->regionals) > 0)
            $consultas->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->headquarters) > 0)
            $consultas->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->qualifiers) > 0)
            $consultas->inQualifiers($this->qualifiers, $this->filtersType['qualifiers']);

        if (COUNT($this->processes) > 0)
            $consultas->inProcesses($this->processes, $this->filtersType['processes']);

        if (COUNT($this->areas) > 0)
            $consultas->inAreas($this->areas, $this->filtersType['areas']);

        if (COUNT($this->themes) > 0)
            $consultas->inThemes($this->themes, $this->filtersType['themes']);

        if (COUNT($this->inspections) > 0)
            $consultas->inInspections($this->inspections, $this->filtersType['inspections'], 'sau_ph_inspections');

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart($consultas);
    }

    private function inspectionsBarType2($column)
    {
        $consultas = InspectionItemsQualificationAreaLocation::select("$column as category", 
            DB::raw('COUNT(DISTINCT sau_ph_inspections.id) AS count'))
            ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
            ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
            ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id')
            //->inThemes($this->themes, $this->filtersType['themes'])
            //->inInspections($this->inspections, $this->filtersType['inspections'])
            ->betweenDate($this->dates)
            ->withoutGlobalScopes()
            ->where('sau_ph_inspections.company_id', $this->company)
            ->where('sau_ph_inspections.type_id', 2)
            ->groupBy('category', 'sau_ph_inspection_items_qualification_area_location.qualification_date');

        if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $this->areasFilter);
        }

        if (COUNT($this->regionals) > 0)
            $consultas->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->headquarters) > 0)
            $consultas->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->processes) > 0)
            $consultas->inProcesses($this->processes, $this->filtersType['processes']);

        if (COUNT($this->qualifiers) > 0)
            $consultas->inQualifiers($this->qualifiers, $this->filtersType['qualifiers']);

        if (COUNT($this->areas) > 0)
            $consultas->inAreas($this->areas, $this->filtersType['areas']);

        if (COUNT($this->themes) > 0)
            $consultas->inThemes($this->themes, $this->filtersType['themes']);

        if (COUNT($this->inspections) > 0)
            $consultas->inInspections($this->inspections, $this->filtersType['inspections'], 'sau_ph_inspections');

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart($consultas);
    }

    private function complianceBar($column)
    {
        $consultas2 = InspectionItemsQualificationAreaLocation::select(
            "$column as category",
            DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
            DB::raw('count(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, null)) as t_cumple'),
            DB::raw('count(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, null)) as t_no_cumple'),
            DB::raw('count(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, null)) as t_cumple_p')
            )
            ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
            ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
            ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id')
            //->inThemes($this->themes, $this->filtersType['themes'])
            //->inInspections($this->inspections, $this->filtersType['inspections'])
            ->betweenDate($this->dates)
            ->withoutGlobalScopes()
            ->where('sau_ph_inspections.company_id', $this->company)
            ->where('sau_ph_inspections.type_id', 1)
            ->groupBy('category');

        if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $this->areasFilter);
        }

        if (COUNT($this->regionals) > 0)
            $consultas2->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->headquarters) > 0)
            $consultas2->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->processes) > 0)
            $consultas2->inProcesses($this->processes, $this->filtersType['processes']);

        if (COUNT($this->qualifiers) > 0)
            $consultas2->inQualifiers($this->qualifiers, $this->filtersType['qualifiers']);

        if (COUNT($this->areas) > 0)
            $consultas2->inAreas($this->areas, $this->filtersType['areas']);

        if (COUNT($this->themes) > 0)
            $consultas2->inThemes($this->themes, $this->filtersType['themes']);

        if (COUNT($this->inspections) > 0)
            $consultas2->inInspections($this->inspections, $this->filtersType['inspections'], 'sau_ph_inspections');

        $consultas2 = DB::table(DB::raw("({$consultas2->toSql()}) AS t"))
        ->select(
            "t.category AS category",
            //DB::raw('ROUND( (t_cumple * 100) / (t_cumple + t_no_cumple), 1) AS p_cumple'),
            //DB::raw('ROUND( (t_no_cumple * 100) / (t_cumple + t_no_cumple), 1) AS p_no_cumple'),
            DB::raw('ROUND( (t_cumple * 100) / (t_cumple + t_no_cumple + t_cumple_p), 1) AS p_cumple'),
            DB::raw('ROUND( (t_no_cumple * 100) / (t_cumple + t_no_cumple + t_cumple_p), 1) AS p_no_cumple'),
            DB::raw('ROUND( (t_cumple_p * 100) / (t_cumple + t_cumple_p + t_no_cumple), 1) AS p_cumple_p')
        )
        ->mergeBindings($consultas2->getQuery())
        ->groupBy('t.category')
        ->get();

        return $this->builderDataCompliance($consultas2);
    }

    private function complianceType2Bar($column)
    {
        $consultas2 = InspectionItemsQualificationAreaLocation::select(
            "$column as category",
            DB::raw('SUM(IF(sau_ph_inspection_items_qualification_area_location.qualification_id = 1, sau_ph_inspection_section_items.compliance_value, 0)) / COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS t_cumple'),
            DB::raw('SUM(IF(sau_ph_inspection_items_qualification_area_location.qualification_id = 4, sau_ph_inspection_section_items.partial_value, 0)) / COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS t_parcial'),
            DB::raw('SUM(IF(sau_ph_inspection_items_qualification_area_location.qualification_id = 2, 0, 0)) / COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS t_no_cumple')
            )
            ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
            ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
            ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id')
            //->inThemes($this->themes, $this->filtersType['themes'])
            //->inInspections($this->inspections, $this->filtersType['inspections'])
            ->betweenDate($this->dates)
            ->withoutGlobalScopes()
            ->where('sau_ph_inspections.company_id', $this->company)
            ->where('sau_ph_inspections.type_id', 2)
            ->groupBy('sau_ph_inspection_sections.id', 'category');

        if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas2->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $this->areasFilter);
        }

        if (COUNT($this->regionals) > 0)
            $consultas2->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->headquarters) > 0)
            $consultas2->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->qualifiers) > 0)
            $consultas2->inQualifiers($this->qualifiers, $this->filtersType['qualifiers']);

        if (COUNT($this->processes) > 0)
            $consultas2->inProcesses($this->processes, $this->filtersType['processes']);

        if (COUNT($this->areas) > 0)
            $consultas2->inAreas($this->areas, $this->filtersType['areas']);

        if (COUNT($this->themes) > 0)
            $consultas2->inThemes($this->themes, $this->filtersType['themes']);

        if (COUNT($this->inspections) > 0)
            $consultas2->inInspections($this->inspections, $this->filtersType['inspections'], 'sau_ph_inspections');

        $consultas2 = DB::table(DB::raw("({$consultas2->toSql()}) AS t"))
        ->select(
            "t.category AS category",
            DB::raw('ROUND(AVG(t_cumple), 1) AS p_cumple'),
            DB::raw('ROUND(AVG(t_parcial), 1) AS p_cumple_p'),
            DB::raw('ROUND((100 - AVG(t_cumple) - AVG(t_parcial)), 1) AS p_no_cumple')
        )
        ->mergeBindings($consultas2->getQuery())
        ->groupBy('t.category')
        ->get();

        return $this->builderDataCompliance($consultas2);
    }

    /**
     * Returns the exposed population reports grouped by each filter type
     * @return collection
     */
    public function inspections()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->inspectionsBar($column[0]));
        }
    
        return $informData->toArray();
    }

    public function inspectionsType2()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->inspectionsBarType2($column[0]));
        }
    
        return $informData->toArray();
    }

    /**
     * Returns the exposed population reports grouped by each filter type
     * @return collection
     */
    public function compliance()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->complianceBar($column[0]));
        }
    
        return $informData->toArray();
    }

    public function complianceType2()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->complianceType2Bar($column[0]));
        }
    
        return $informData->toArray();
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
            $label2 = strlen($label) > 50 ? substr($this->sanear_string($label), 0, 50).'...' : $label;
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

    private function builderDataCompliance($data)
    {
        $labels = collect([]);
        $cumple = collect([]);
        $no_cumple = collect([]);
        $parcial = collect([]);

        foreach ($data as $key => $value)
        {
            $label = strlen($value->category) > 30 ? substr($this->sanear_string($value->category), 0, 30).'...' : $value->category;

            $labels->push($label);
            $cumple->push($value->p_cumple);
            $no_cumple->push($value->p_no_cumple);
            $parcial->push($value->p_cumple_p);
        }

        $info = [
            "name" => 'Cumple',
            "type" => 'bar',
            "stack" => 'barras',
            "data" => $cumple->values(),
            "itemStyle" => ['color' => '#008f39'],
            "label" => [
                "normal" => [
                    "show" => true,
                    "color" => "white",
                    "formatter" => '{c}%',
                ]
            ]
        ];

        $info2 = [
            "name" => 'No Cumple',
            "type" => 'bar',
            "stack" => 'barras',
            "data" => $no_cumple->values(),
            "itemStyle" => ['color' => '#f0635f'],
            "label" => [
                "normal" => [
                    "show" => true,
                    "color" => "white",
                    "formatter" => '{c}%',
                ]
            ]
        ];

        $info3 = [
            "name" => 'Parcial',
            "type" => 'bar',
            "stack" => 'barras',
            "data" => $parcial->values(),
            "itemStyle" => ['color' => '#E5B601'],
            "label" => [
                "normal" => [
                    "show" => true,
                    "color" => "white",
                    "formatter" => '{c}%',
                ]
            ]
        ];

        return collect([
            //'legend' => ['Cumple', 'No Cumple'],
            'legend' => ['Cumple', 'No Cumple', 'Parcial'],
            'labels' => $labels,
            'datasets' => [
                //'data' => [$info, $info2],
                'data' => [$info, $info2, $info3],
                'type' => 'percentage',
                'count' => 0,
            ]
        ]);
    }

}