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
        'evaluations'
        //'compliance'
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

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($company = '',$contract = '', $evaluations = '', $objectives = '', $subobjectives = '', $items = '', $type_ratings = '', $dates = '')
    {
        $this->company = $company;
        $this->contract = $contract;
        $this->evaluations = $evaluations;
        $this->objectives = $objectives;
        $this->subobjectives = $subobjectives;
        $this->items = $items;
        $this->type_ratings = $type_ratings;
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
            $informData->put($column[1], $this->evaluationBar($column[0]));
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