<?php

namespace App\Inform\LegalAspects\LegalMatrix;

use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Models\LegalAspects\LegalMatrix\SystemApply;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\QualificationColorDinamic;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Session;
use DB;

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
        'resumenFulfillment',
        'reportTableDinamic',
        'fulfillmentPie',
        'reportTableRisk'
        //'colors'
    ];

    const CATEGORY_COLUMNS = [
        "lawType" => "sau_lm_laws_types.name",
        "riskAspects" => "sau_lm_risks_aspects.name",
        "riskSst" => "sau_lm_sst_risks.name",
        "entity" => "sau_lm_entities.name",
        "systemApply" => "sau_lm_system_apply.name",
        "year" => "sau_lm_laws.law_year",
        "repealed" => "sau_lm_laws.repealed",
        "interest" => "sau_lm_interests.name"
    ];

    protected $table_fulfillment = [
        "Sin calificar" => ["count" => 1, "qualify" => 0],
        "Cumple"        => ["count" => 1, "qualify" => 1],
        "No cumple"     => ["count" => 1, "qualify" => 0],
        "En estudio"    => ["count" => 1, "qualify" => 0],
        "Parcial"       => ["count" => 1, "qualify" => 0.5],
        "No aplica"     => ["count" => 0, "qualify" => 0],
        "Informativo"   => ["count" => 0, "qualify" => 0],
        "No vigente"    => ["count" => 0, "qualify" => 0],
        "En Transición" => ["count" => 0, "qualify" => 0],
        "Pendiente reglamentación" => ["count" => 0, "qualify" => 0]
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
    protected $articles_t = 0;
    protected $articles_c = 0;
    protected $articles_nc = 0;
    protected $articles_partial = 0;
    protected $category;
    protected $qualificationsTypes;
    protected $dates;
    protected $company;
    protected $useRiskOppoortunity;
    protected $riskOpportunity;

    /**
     * create an instance and set the attribute class
     * @param array $lawTypes
     */
    function __construct($lawTypes = [], $riskAspects = [], $entities = [], $sstRisks = [], $systemApply = [], $lawNumbers = [], $lawYears = [], $repealed = [], $responsibles = [], $interests = [], $states = [], $filtersType = [], $category = '', $dates = [], $riskOpportunity = [])
    {
        $this->company = Session::get('company_id');
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
        $this->riskOpportunity = $riskOpportunity;
        $this->states = $states;
        $this->filtersType = $filtersType;
        $this->totalLaws = $this->getTotalLaws();
        $this->category = $category;
        $this->dates = $dates ? $dates : [];
        $this->useRiskOppoortunity = $this->getLegalMatrixRiskOpport();
    }

    public function getLegalMatrixRiskOpport()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'legal_matrix_risk_opportunity');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
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

            if ($component == 'fulfillmentPie')
            {
                $colors = $this->colors($this->$component());
                $informData->put('colors', $colors);
            }
        }
        return $informData->toArray();
    }

    public function colors($data)
    {
        $colors = QualificationColorDinamic::first();

        $colors_company = [];

        if ($colors)
        {
            if ($colors->sin_calificar == 'ffffff' && $colors->cumple == 'ffffff' && $colors->no_cumple == 'ffffff' && $colors->en_estudio == 'ffffff' && $colors->parcial == 'ffffff' && $colors->no_aplica == 'ffffff' && $colors->informativo == 'ffffff' && $colors->no_vigente == 'ffffff' && $colors->en_transicion == 'ffffff' && $colors->pendiente_reglamentacion == 'ffffff')
            {
                $colors_company = [];
            }
            else
            {
                foreach ($data['labels'] as $key => $qualification) 
                {
                    if ($qualification == 'Sin calificar')
                        array_push($colors_company, $colors->sin_calificar ? '#'.$colors->sin_calificar : '#ffffff');
                    else if ($qualification == 'Cumple')
                        array_push($colors_company, $colors->cumple ? '#'.$colors->cumple : '#ffffff');
                    else if ($qualification == 'No cumple')
                        array_push($colors_company, $colors->no_cumple ? '#'.$colors->no_cumple : '#ffffff');
                    else if ($qualification == 'En estudio')
                        array_push($colors_company, $colors->en_estudio ? '#'.$colors->en_estudio : '#ffffff');
                    else if ($qualification == 'Parcial')
                        array_push($colors_company, $colors->parcial ? '#'.$colors->parcial : '#ffffff');
                    else if ($qualification == 'No aplica')
                        array_push($colors_company, $colors->no_aplica ? '#'.$colors->no_aplica : '#ffffff');
                    else if ($qualification == 'Informativo')
                        array_push($colors_company, $colors->informativo ? '#'.$colors->informativo : '#ffffff');
                    else if ($qualification == 'No vigente')
                        array_push($colors_company, $colors->no_vigente ? '#'.$colors->no_vigente : '#ffffff');
                    else if ($qualification == 'En Transición')
                        array_push($colors_company, $colors->en_transicion ? '#'.$colors->en_transicion : '#ffffff');
                    else if ($qualification == 'Pendiente reglamentación')
                        array_push($colors_company, $colors->pendiente_reglamentacion ? '#'.$colors->pendiente_reglamentacion : '#ffffff');
                }
                
            }
        }

        return $colors_company;
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
        //s->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
        ->join('sau_lm_company_interest', function ($join) 
        {
          $join->on("sau_lm_company_interest.interest_id", "sau_lm_article_interest.interest_id"); 
          $join->on("sau_lm_company_interest.company_id", "=", DB::raw("{$this->company}"));
        })
        ->join('sau_lm_articles_fulfillment', function ($join) 
        {
          $join->on("sau_lm_articles_fulfillment.article_id", "sau_lm_articles.id"); 
          $join->on("sau_lm_articles_fulfillment.company_id", "=", DB::raw("{$this->company}"));
        })
        //->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
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
        ->betweenDate($this->dates ? $this->dates : [])
        ->inRiskOpportunity($this->riskOpportunity, $this->filtersType['riskOpportunity'])
        ->groupBy('system_apply', 'qualify')
        ->get();

        $qualifications = [];

        foreach ($laws as $key => $value)
        {
            $qualifications[$value->qualify][$value->system_apply] = $value->count;
            
            $this->totalArticles += $value->count;
            $this->articles_t += ($value->count * $this->table_fulfillment[$value->qualify]["count"]);
            $this->articles_c += ($value->count * $this->table_fulfillment[$value->qualify]["qualify"]);

            if ($value->qualify == 'Parcial') 
                $this->articles_partial += ($value->count * $this->table_fulfillment[$value->qualify]["qualify"]);
        }

        $values_fulfillments = $laws->pluck('qualify')->unique();

        $fulfillments = FulfillmentValues::whereIn('name', $values_fulfillments)->get();
        $system_apply = SystemApply::alls()->inSystemApply($this->systemApply, $this->filtersType['systemApply'])->get();
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

    private function fulfillmentPie()
    {
        $laws = Law::selectRaw(
            'IF(sau_lm_fulfillment_values.name IS NULL, "Sin calificar", sau_lm_fulfillment_values.name) AS qualify,
             COUNT(DISTINCT sau_lm_articles_fulfillment.id) AS count'
        )
        ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
        ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
        ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
        //->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
        //->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
        ->join('sau_lm_company_interest', function ($join) 
        {
          $join->on("sau_lm_company_interest.interest_id", "sau_lm_article_interest.interest_id"); 
          $join->on("sau_lm_company_interest.company_id", "=", DB::raw("{$this->company}"));
        })
        ->join('sau_lm_articles_fulfillment', function ($join) 
        {
          $join->on("sau_lm_articles_fulfillment.article_id", "sau_lm_articles.id"); 
          $join->on("sau_lm_articles_fulfillment.company_id", "=", DB::raw("{$this->company}"));
        })
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
        ->betweenDate($this->dates ? $this->dates : [])
        ->inRiskOpportunity($this->riskOpportunity, $this->filtersType['riskOpportunity'])
        ->groupBy('qualify')
        ->get();

        $qualifications = [];

        foreach ($laws as $key => $value)
        {
            $qualifications[$value->qualify] = $value->count;
        }

        $values_fulfillments = $laws->pluck('qualify')->unique();

        $fulfillments = FulfillmentValues::whereIn('name', $values_fulfillments)->get();
        $data = collect([]);

        foreach ($fulfillments as $fulfillment)
        {
            $qualify = isset($qualifications[$fulfillment->name]) ? $qualifications[$fulfillment->name] : 0;
            $data->push(['name' => $fulfillment->name, "count" => $qualify]);
        }

        return $this->buildDataChart($data);
    }

    protected function buildDataChart($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $count) {
            array_push($labels, $count['name']);
            array_push($data, ['name' => $count['name'], 'value' => $count['count']]);
            $total += $count['count'];
        }

        return collect([
            'labels' => $labels,
            'datasets' => [
                'table' => $this->buildTable($rawData),
                'data' => $data,
                'count' => $total
            ]
        ]);
    }

    private function buildTable($data)
    {
        $result = [];

        foreach ($data as $value)
        {
            array_push($result, [
                $value['name'], $value['count']
            ]);
        }

        return $result;
    }

    private function reportTableDinamic()
    {
        $column = $this->category ? $this::CATEGORY_COLUMNS[$this->category] : $this::CATEGORY_COLUMNS['systemApply'];

        $laws = Law::selectRaw(
            'IF(sau_lm_fulfillment_values.name IS NULL, "Sin calificar", sau_lm_fulfillment_values.name) AS qualify,
             '. $column .' AS category,
             COUNT(DISTINCT sau_lm_articles_fulfillment.id) AS count'
        )
        ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
        ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
        ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
        //->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
        ->join('sau_lm_company_interest', function ($join) 
        {
          $join->on("sau_lm_company_interest.interest_id", "sau_lm_article_interest.interest_id"); 
          $join->on("sau_lm_company_interest.company_id", "=", DB::raw("{$this->company}"));
        })
        ->join('sau_lm_articles_fulfillment', function ($join) 
        {
          $join->on("sau_lm_articles_fulfillment.article_id", "sau_lm_articles.id"); 
          $join->on("sau_lm_articles_fulfillment.company_id", "=", DB::raw("{$this->company}"));
        })
        //->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
        ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
        ->join('sau_lm_interests', 'sau_lm_interests.id', 'sau_lm_company_interest.interest_id')
        ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
        ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
        ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
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
        ->betweenDate($this->dates ? $this->dates : [])
        ->inRiskOpportunity($this->riskOpportunity, $this->filtersType['riskOpportunity'])
        ->groupBy('category', 'qualify')
        ->orderBy('category')
        ->get();

        $qualificationsTypes = array_keys($this->table_fulfillment);

        $laws = $laws->groupBy('category');

        $result = collect([]);

        foreach ($laws as $category => $rows)
        {
            $category_total = 0;
            $item = collect([]);
            $item->put('category', $category);

            foreach ($qualificationsTypes as $qualification)
            {
                $item->put($qualification, 0);
            }

            foreach ($rows as $row)
            {
                $category_total = $category_total + $row->count;
                $item->put($row->qualify, $row->count);
            }

            $item->put('Total', $category_total);

            $result->push($item);
        }

        return $result;
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
        $articles_nc = ($this->articles_t - $this->articles_c - $this->articles_partial);
        $percentage_fulfillment = ($this->articles_t > 0) ? round( ($this->articles_c / $this->articles_t) * 100, 1) : 0;
        $percentage_no_fulfillment = ($this->articles_t > 0) ? round( ($articles_nc / $this->articles_t) * 100, 1) : 0;

        return [
            "total_laws" => $this->totalLaws,
            "total_articles" => $this->totalArticles,
            "articles_t" => $this->articles_t,
            "articles_c" => $this->articles_c,
            "articles_nc" => $articles_nc,
            "percentage_c" => $percentage_fulfillment.'%',
            "percentage_nc" => $percentage_no_fulfillment.'%',
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
        //->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
        ->join('sau_lm_articles_fulfillment', function ($join) 
        {
          $join->on("sau_lm_articles_fulfillment.article_id", "sau_lm_articles.id"); 
          $join->on("sau_lm_articles_fulfillment.company_id", "=", DB::raw("{$this->company}"));
        })
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
        ->betweenDate($this->dates ? $this->dates : []);

        if (isset($this->filtersType['riskOpportunity']))
            $laws->inRiskOpportunity($this->riskOpportunity, $this->filtersType['riskOpportunity']);

        $laws = $laws->first();

        return $laws ? $laws->total : 0;
    }

    private function reportTableRisk()
    {
        if ($this->useRiskOppoortunity == 'SI')
        {
            $laws = Law::selectRaw(
                "sau_lm_system_apply.name AS category,
                SUM(IF(sau_lm_law_risk_opportunity.type = 'Riesgo' OR sau_lm_law_risk_opportunity.type = 'Riesgo y oportunidad', 1, 0)) AS count_risk,
                SUM(IF(sau_lm_law_risk_opportunity.type = 'Oportunidad' OR sau_lm_law_risk_opportunity.type = 'Riesgo y oportunidad', 1, 0)) AS count_opport,
                SUM(IF(sau_lm_law_risk_opportunity.type = 'No aplica', 1, 0)) AS count_n_a"
            )
            ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
            ->join('sau_lm_law_risk_opportunity', function ($join) 
            {
            $join->on("sau_lm_law_risk_opportunity.law_id", "sau_lm_laws.id"); 
            $join->on("sau_lm_law_risk_opportunity.company_id", "=", DB::raw("{$this->company}"));
            })
            ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
            ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
            ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
            ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
            ->inLawTypes($this->lawTypes, $this->filtersType['lawTypes'])
            ->inRiskAspects($this->riskAspects, $this->filtersType['riskAspects'])
            ->inEntities($this->entities, $this->filtersType['entities'])
            ->inSstRisks($this->sstRisks, $this->filtersType['sstRisks'])
            ->inSystemApply($this->systemApply, $this->filtersType['systemApply'])
            ->inLawNumbers($this->lawNumbers, $this->filtersType['lawNumbers'])
            ->inLawYears($this->lawYears, $this->filtersType['lawYears'])
            ->inRepealed($this->repealed, $this->filtersType['repealed'])
            ->inResponsibles($this->responsibles,$this->filtersType['responsibles'])
            ->inInterestsCompany($this->interests,$this->filtersType['interests'])
            ->inState($this->states,$this->filtersType['states'])
            ->betweenDateRiskOpportunity($this->dates ? $this->dates : [])
            ->groupBy('category')
            ->orderBy('category')
            ->get();

            return $laws;
        }
        else
            return [];
    }
}