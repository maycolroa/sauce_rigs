<?php

namespace App\Inform\LegalAspects\Contract\Evaluations;

use App\Models\LegalAspects\Contracts\EvaluationContract;
use DB;

class InformManagerEvaluationContract
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
        'evaluations',
        'compliance'
    ];

    const GROUPING_COLUMNS = [
        ['c.social_reason', 'contract'],
        ['e.name', 'evaluation'],
        ['o.description', 'objective'],
        ['s.description', 'subobjective'],
        ['i.description', 'item'],
        ['tr.name', 'type_rating']
    ];

    const TYPE_PERCENTAGE = [
        'total' => [],
        'percentage_x_category' => []
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $contract;
    protected $evaluations;
    protected $objectives;
    protected $subobjectives;
    protected $items;
    protected $type_ratings;
    protected $company;
    protected $filtersType;
    protected $dates;
    protected $subWhereQualificationTypes;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($company = '',$contract = '', $evaluations = '', $objectives = '', $subobjectives = '', $items = '', $type_ratings = '', $dates = '', $subWhereQualificationTypes = '')
    {
        $this->company = $company;
        $this->contract = $contract;
        $this->evaluations = $evaluations;
        $this->objectives = $objectives;
        $this->subobjectives = $subobjectives;
        $this->items = $items;
        $this->type_ratings = $type_ratings;
        $this->dates = $dates;
        $this->subWhereQualificationTypes = $subWhereQualificationTypes;
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
    private function evaluationBar($column)
    {
        $evaluations = DB::table(DB::raw("(
                SELECT ".$column." as category,
                COUNT(DISTINCT ec.id) as count

                    FROM sau_ct_evaluation_contract ec
                    INNER JOIN sau_ct_information_contract_lessee c ON c.id = ec.contract_id
                    INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                    INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                    INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
                    INNER JOIN sau_ct_items i ON i.subobjective_id = s.id
                    LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id AND eir.evaluation_id = ec.id
                    LEFT JOIN sau_ct_types_ratings tr ON tr.id = eir.type_rating_id
                
                    WHERE ".$column." <> '' AND ".$column." IS NOT NULL AND ec.company_id = ".$this->company. $this->dates . $this->objectives . $this->subobjectives . $this->type_ratings . $this->contract . $this->items . $this->evaluations ."
                    GROUP BY category
            ) AS t"))
        ->pluck('count', 'category');

        return $this->buildDataChart($evaluations);
    }

    private function complianceBar($column, $column2)
    {
        $compliance = DB::table(DB::raw("(
            SELECT ".$column2." as category,
                ROUND( (SUM(t_cumple) * 100) / (SUM(t_cumple) + SUM(t_no_cumple)), 1) AS p_cumple,
                ROUND( (SUM(t_no_cumple) * 100) / (SUM(t_cumple) + SUM(t_no_cumple)), 1) AS p_no_cumple 
            FROM (
            
                SELECT 
                    ec.id as id,
                    e.name as evaluation,
                    o.description as objective,
                    s.description as subobjective,
                    i.description as item,
                    c.social_reason as contract,
                    tr.name as type_rating,
                    SUM(IF(eir.value = 'NO' OR eir.value = 'pending', 1, 0)) AS t_no_cumple,
                    SUM(IF(eir.value = 'SI', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
                                IF(eir.value IS NULL AND eir.item_id IS NULL,
                                    (SELECT 
                                            COUNT(etr.type_rating_id)
                                        FROM
                                            sau_ct_evaluation_type_rating etr
                                        WHERE
                                            etr.evaluation_id = e.id {$this->subWhereQualificationTypes}
                                    )
                                , 0)
                            )
                        )
                    ) AS t_cumple
                
                    FROM sau_ct_evaluation_contract ec
                    INNER JOIN sau_ct_information_contract_lessee c ON c.id = ec.contract_id
                    INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                    INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                    INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
                    INNER JOIN sau_ct_items i ON i.subobjective_id = s.id
                    LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id AND eir.evaluation_id = ec.id
                    LEFT JOIN sau_ct_types_ratings tr ON tr.id = eir.type_rating_id

                    WHERE ".$column." <> '' AND ".$column." IS NOT NULL AND ec.company_id = ".$this->company. $this->dates . $this->objectives . $this->subobjectives . $this->type_ratings . $this->contract . $this->items . $this->evaluations ." 
                    GROUP BY id, evaluation, objective, subobjective, item, contract, type_rating
                ) AS t 
                GROUP BY category
            ) AS t"))
        ->get();
        
        return $this->builderDataCompliance($compliance);
    }

    /**
     * Returns the exposed population reports grouped by each filter type
     * @return collection
     */
    public function evaluations()
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->evaluationBar($column[0]));
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
            $informData->put($column[1], $this->complianceBar($column[0], $column[1]));
        }
    
        return $informData->toArray();
    }

    private function builderDataCompliance($data)
    {
        $labels = collect([]);
        $cumple = collect([]);
        $no_cumple = collect([]);

        foreach ($data as $key => $value)
        {
            $label = strlen($value->category) > 30 ? substr($value->category, 0, 30).'...' : substr($value->category, 0, 30);
            \Log::info($label);
            $labels->push($label);
            $cumple->push($value->p_cumple);
            $no_cumple->push($value->p_no_cumple);
        }

        $info = [
            "name" => 'Cumple',
            "type" => 'bar',
            "stack" => 'barras',
            "data" => $cumple->values(),
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
            "label" => [
                "normal" => [
                    "show" => true,
                    "color" => "white",
                    "formatter" => '{c}%',
                ]
            ]
        ];

        return collect([
            'legend' => ['Cumple', 'No Cumple'],
            'labels' => $labels,
            'datasets' => [
                'data' => [$info, $info2],
                'type' => 'percentage',
                'count' => 0,
            ]
        ]);
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
            $label2 = strlen($label) > 30 ? substr($label, 0, 30).'...' : substr($label, 0, 30);
            array_push($labels, $label2);
            array_push($data, ['name' => $label2, 'value' => $count]);
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