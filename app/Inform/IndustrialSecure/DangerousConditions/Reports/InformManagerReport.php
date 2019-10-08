<?php

namespace App\Inform\IndustrialSecure\DangerousConditions\Reports;

use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\Administrative\Employees\Employee;
use DB;

class InformManagerReport
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
        'report_per_headquarter',
        'report_per_area',
        'report_per_user',
        'report_per_headquarter_area',
    ];

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct()
    {

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
        ->groupBy('sau_employees_headquarters.name', 'sau_employees_areas.name')
        ->orderBy('name')
        ->pluck('count', 'name');

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

    public function locationWithCondition($headquarters)
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

    }
}