<?php

namespace App\Inform\LegalAspects\LegalMatrix;

use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Models\LegalAspects\LegalMatrix\SystemApply;
use App\Models\LegalAspects\LegalMatrix\Law;
use Session;

class ReportManagerLaw
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
        'fulfillment',
        'resumenFulfillment'
    ];

    protected $table_fulfillment = [
        "Sin calificar" => ["count" => 1, "qualify" => 0],
        "Cumple"        => ["count" => 1, "qualify" => 1],
        "No cumple"     => ["count" => 1, "qualify" => 0],
        "En estudio"    => ["count" => 1, "qualify" => 0],
        "Parcial"       => ["count" => 1, "qualify" => 0.5],
        "No aplica"     => ["count" => 0, "qualify" => 0],
        "Informativo"   => ["count" => 0, "qualify" => 0]
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $lawTypes;
    protected $riskAspects;
    protected $entities;
    protected $sstRisks;
    protected $systemApply;
    protected $lawNumbers;
    protected $lawYears;
    protected $repealed;
    protected $responsibles;
    protected $interests;
    protected $states;
    protected $filtersType;
    protected $totalLaws;
    protected $totalArticles = 0;
    protected $punctuation = 0;

    /**
     * create an instance and set the attribute class
     * @param array $lawTypes
     */
    function __construct($lawTypes = [], $riskAspects = [], $entities = [], $sstRisks = [], $systemApply = [], $lawNumbers = [], $lawYears = [], $repealed = [], $responsibles = [], $interests = [], $states = [], $filtersType = [])
    {
        $this->lawTypes = $lawTypes;
        $this->riskAspects = $riskAspects;
        $this->entities = $entities;
        $this->sstRisks = $sstRisks;
        $this->systemApply = $systemApply;
        $this->lawNumbers = $lawNumbers;
        $this->lawYears = $lawYears;
        $this->repealed = $repealed;
        $this->responsibles = $responsibles;
        $this->interests = $interests;
        $this->states = $states;
        $this->filtersType = $filtersType;
        $this->totalLaws = $this->getTotalLaws();
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

    private function fulfillment()
    {
        $laws = Law::selectRaw(
            'IF(sau_lm_fulfillment_values.name IS NULL, "Sin calificar", sau_lm_fulfillment_values.name) AS qualify,
             sau_lm_system_apply.name AS system_apply,
             COUNT(DISTINCT sau_lm_articles_fulfillment.id) AS count'
        )
        ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
        ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
        ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
        ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
        ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
        ->leftJoin('sau_lm_fulfillment_values','sau_lm_fulfillment_values.id', 'sau_lm_articles_fulfillment.fulfillment_value_id')
        ->where('sau_lm_articles_fulfillment.company_id', Session::get('company_id'))
        ->inLawTypes($this->lawTypes, $this->filtersType['lawTypes'])
        ->inRiskAspects($this->riskAspects, $this->filtersType['riskAspects'])
        ->inEntities($this->entities, $this->filtersType['entities'])
        ->inSstRisks($this->sstRisks, $this->filtersType['sstRisks'])
        ->inSystemApply($this->systemApply, $this->filtersType['systemApply'])
        ->inLawNumbers($this->lawNumbers, $this->filtersType['lawNumbers'])
        ->inLawYears($this->lawYears, $this->filtersType['lawYears'])
        ->inRepealed($this->repealed, $this->filtersType['repealed'])
        ->inResponsibles($this->responsibles,$this->filtersType['responsibles'])
        ->inInterests($this->interests,$this->filtersType['interests'])
        ->inState($this->states,$this->filtersType['states'])
        ->groupBy('system_apply', 'qualify')
        ->get();

        $qualifications = [];

        foreach ($laws as $key => $value)
        {
            $qualifications[$value->qualify][$value->system_apply] = $value->count;
            $this->totalArticles += ($value->count * $this->table_fulfillment[$value->qualify]["count"]);
            $this->punctuation += ($value->count * $this->table_fulfillment[$value->qualify]["qualify"]);
        }

        $fulfillments = FulfillmentValues::get();
        $system_apply = SystemApply::alls()->get();
        $data = collect([]);
        $barSeries = $system_apply->pluck('name')->toArray();

        foreach ($fulfillments as $fulfillment)
        {
            foreach ($system_apply as $system)
            {
                $qualify = isset($qualifications[$fulfillment->name]) ? (isset($qualifications[$fulfillment->name][$system->name]) ? $qualifications[$fulfillment->name][$system->name] : 0) : 0;
                $data->push((object)['name' => $fulfillment->name, "count" => $qualify, "serie" => $system->name]);
            }
        }

        return $this->buildMultiBarDataChart($data, $barSeries);
    }

    /**
     * takes the raw data collection and builds
     * a new collection with the right structure for the multibar
     * chart data
     * @param  collection $rawData
     * @return collection
     */
    protected function buildMultiBarDataChart($rawData, $barSeries)
    {
        $labels = [];
        $data = [];
        $total = [];
        $series = [];
        $max_value = 0;
        $total_divisor = 0;

        foreach ($barSeries as $value)
        {
            $series[$value] = [];
            $total[$value] = 0;
        }

        foreach ($rawData as $item)
        {
            if (!isset($labels[$item->name]))    
                $labels[$item->name] = $item->name;

            $series[$item->serie][$item->name] = ['name' => $item->name, 'value' => $item->count];

            foreach ($barSeries as $value)
            {
                if (!isset($series[$value][$item->name]))
                {
                    $series[$value][$item->name] = ['name' => $item->name, 'value' => 0];
                }
            }

            $total[$item->serie] += $item->count;

            if ($item->count > $max_value)
                $max_value = $item->count;
        }

        foreach ($series as $key => $value) 
        {
            $info = [
                "name" => $key,
                "type" => 'bar',
                "data" => array_values($value),
                "label" => [
                    "normal" => [
                        "show" => true,
                        "position" => "right",
                        "color" => "black",
                        "formatter" => "{c}"
                    ]
                ]
            ];

            array_push($data, $info);
            $total_divisor = COUNT($value);
        }

        /**Divisor de series */
        /*$data_divisor = [];

        for ($i=0; $i < $total_divisor; $i++)
        { 
            array_push($data_divisor, $max_value);
        }

        $divisor = [
            "name" => '',
            "type" => 'bar',
            "barWidth" => 3,
            "data" => $data_divisor
        ];

        array_push($data, $divisor);*/
        /************************* */

        return collect([
            'labels' => array_values($labels),
            'datasets' => [
                'data' => $data,
                'count' => $total,
                'series' => $barSeries
            ]
        ]);
    }

    private function resumenFulfillment()
    {
        $percentage_fulfillment = ($this->totalArticles > 0) ? round( ($this->punctuation / $this->totalArticles) * 100, 1).'%' : 0;

        return [
            "total_laws" => $this->totalLaws,
            "total_articles" => $this->totalArticles,
            "punctuation" => $this->punctuation,
            "percentage_fulfillment" => $percentage_fulfillment
        ];
    }

    /**
     * Return the total amount of exposed population
     *
     * @return void
     */
    private function getTotalLaws()
    {
        $laws = Law::selectRaw(
            'COUNT(DISTINCT sau_lm_laws.id) AS total'
        )
        ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
        ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
        ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
        ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
        ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
        ->leftJoin('sau_lm_fulfillment_values','sau_lm_fulfillment_values.id', 'sau_lm_articles_fulfillment.fulfillment_value_id')
        ->where('sau_lm_articles_fulfillment.company_id', Session::get('company_id'))
        ->inLawTypes($this->lawTypes, $this->filtersType['lawTypes'])
        ->inRiskAspects($this->riskAspects, $this->filtersType['riskAspects'])
        ->inEntities($this->entities, $this->filtersType['entities'])
        ->inSstRisks($this->sstRisks, $this->filtersType['sstRisks'])
        ->inSystemApply($this->systemApply, $this->filtersType['systemApply'])
        ->inLawNumbers($this->lawNumbers, $this->filtersType['lawNumbers'])
        ->inLawYears($this->lawYears, $this->filtersType['lawYears'])
        ->inRepealed($this->repealed, $this->filtersType['repealed'])
        ->inResponsibles($this->responsibles,$this->filtersType['responsibles'])
        ->inInterests($this->interests,$this->filtersType['interests'])
        ->inState($this->states,$this->filtersType['states'])
        ->first();

        return $laws->total;
    }
}