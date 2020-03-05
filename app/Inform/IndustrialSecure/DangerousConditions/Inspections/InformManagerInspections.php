<?php

namespace App\Inform\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspections;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use DB;

class InformManagerInspections
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
        'inspections',
        //'compliance'
    ];

    const GROUPING_COLUMNS = [
        ['sau_employees_headquarters.name', 'headquarter'],
        ['sau_employees_areas.name', 'area'],
        ['sau_ph_inspection_sections.name', 'theme'], 
        ['sau_ph_inspections.name', 'inspection']
    ];

    protected $headquarters;
    protected $areas;
    protected $themes;
    protected $filtersType;
    protected $dates;
    protected $company;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($company = '', $headquarters = '', $areas = '', $themes = '', $filtersType = '', $dates = '')
    {
        $this->company = $company;
        $this->headquarters = $headquarters;
        $this->areas = $areas;
        $this->themes = $themes;
        $this->filtersType = $filtersType;
        $this->dates = $dates;

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
            ->join('sau_ct_qualifications','sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->join('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id' )
            ->inHeadquarters($this->headquarters, $this->filtersType['headquarters'])
            ->inAreas($this->areas, $this->filtersType['areas'])
            ->inThemes($this->themes, $this->filtersType['themes'])
            ->betweenDate($this->dates)
            ->where('sau_ph_inspections.company_id', $this->company)
            ->groupBy('category', 'sau_ph_inspection_items_qualification_area_location.qualification_date');

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        \Log::info($consultas);

        return $this->buildDataChart($consultas);
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