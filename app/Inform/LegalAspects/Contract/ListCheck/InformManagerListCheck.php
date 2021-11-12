<?php

namespace App\Inform\LegalAspects\Contract\ListCheck;

use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\ListCheckResumen;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Traits\UtilsTrait;
use DB;

class InformManagerListCheck
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
        'contracts',
        'standar'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $company_id;
    protected $contracts;
    protected $classification;
    protected $itemStandar;
    protected $filtersType;

    /**
     * create an instance and set the attribute class
     * @param array $identifications
     */
    function __construct($company_id, $contracts = [], $classification = [], $itemStandar = [], $filtersType = [])
    {
        $this->company_id = $company_id;
        $this->contracts = $contracts;
        $this->classification = $classification;
        $this->itemStandar = $itemStandar;
        $this->filtersType = $filtersType;

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
     * return the open reports bar data for the view
     * @return collection
     */
    public function contracts()
    {
        $contracts = ContractLesseeInformation::selectRaw("
            CASE
                WHEN total_p_c BETWEEN 0 AND 60 THEN 'Crítico < 60%'
                WHEN total_p_c BETWEEN 61 AND 85 THEN 'Moderadamente Aceptable 60% - 85%'
                WHEN total_p_c > 85 THEN 'Aceptable > 85%'
                ELSE 'Crítico < 60%' END AS label,
            CASE
                WHEN total_p_c BETWEEN 0 AND 35 THEN 1
                WHEN total_p_c BETWEEN 36 AND 70 THEN 2
                WHEN total_p_c > 71 THEN 3
                ELSE 1 END AS orden,
            COUNT(*) AS total
        ")
        ->join('sau_ct_list_check_qualifications', function ($join) 
            {
                $join->on("sau_ct_list_check_qualifications.contract_id", "sau_ct_information_contract_lessee.id");
                $join->on('sau_ct_list_check_qualifications.state', DB::raw(1));
            })
        ->leftJoin('sau_ct_list_check_resumen', 'sau_ct_list_check_resumen.list_qualification_id', 'sau_ct_list_check_qualifications.id')
        ->inContracts($this->contracts, $this->filtersType['contracts'])
        ->inClassification($this->classification, $this->filtersType['classification'])
        ->where('sau_ct_information_contract_lessee.type', 'Contratista')
        ->groupBy('label', 'orden')
        ->orderBy('orden', 'DESC')
        ->pluck('total', 'label');

        return $this->buildDataChart($contracts);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function standar()
    {
        $items_apply = ContractLesseeInformation::selectRaw("
            id AS contract_id,
            CASE 
                WHEN (classification = 'UPA' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '3 Estándares'
                WHEN (classification = 'Empresa' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '7 Estándares'
                WHEN (classification = 'Empresa' AND number_workers BETWEEN 11 AND 50 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '21 Estándares'
                WHEN (
                    (classification = 'UPA' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) OR
                    (classification = 'Empresa' AND number_workers BETWEEN 11 AND 50 AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) OR 
                    (classification = 'Empresa' AND number_workers > 50) 
                     OR 
                    (classification = 'Empresa' AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) 
                    )THEN '60 Estándares'
            ELSE NULL END AS standard_name")
        ->withoutGlobalScopes()        
        ->inContracts($this->contracts, $this->filtersType['contracts'])
        ->inClassification($this->classification, $this->filtersType['classification'])
        ->where('sau_ct_information_contract_lessee.company_id', $this->company_id)
        ->havingRaw("standard_name IS NOT NULL");

        //$items_apply->company_scope = $this->company_id;

        $compliance = SectionCategoryItems::selectRaw("
                sau_ct_section_category_items.item_name AS category,
                SUM(CASE WHEN sau_ct_item_qualification_contract.qualification_id IN (1,3) THEN 1 ELSE 0 END) AS total_c,
                SUM(CASE WHEN sau_ct_item_qualification_contract.qualification_id = 2 OR sau_ct_item_qualification_contract.qualification_id IS NULL THEN 1 ELSE 0 END) AS total_nc
                ")
            ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
            ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id')
            ->join(DB::raw("({$items_apply->toSql()}) as t"), function ($join) 
            {
                $join->on("t.standard_name", "sau_ct_standard_classification.standard_name");
            })
            ->leftJoin('sau_ct_item_qualification_contract', function ($join) 
            {
                $join->on("sau_ct_item_qualification_contract.contract_id", "t.contract_id");
                $join->on("sau_ct_item_qualification_contract.item_id", "sau_ct_section_category_items.id");
            })
            ->groupBy('sau_ct_section_category_items.item_name')
            ->mergeBindings($items_apply->getQuery())            
            ->inStandard($this->itemStandar, $this->filtersType['itemStandar']);

            $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');

            /*if ($exist == 'SI')
                $compliance->where('sau_ct_item_qualification_contract.state_aprove_qualification', 'APROBADA');*/
            
            $compliance = $compliance->orderBy('category')->get();

        return $this->builderDataCompliance($compliance);
    
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

    private function builderDataCompliance($data)
    {
        $labels = collect([]);
        $cumple = collect([]);
        $no_cumple = collect([]);

        foreach ($data as $key => $value)
        {
            $label = strlen($value->category) > 30 ? substr($this->sanear_string($value->category), 0, 30).'...' : $value->category;

            $total = $value->total_c + $value->total_nc;

            $labels->push($label);
            $cumple->push(round(($value->total_c / $total) * 100));
            $no_cumple->push(round(($value->total_nc / $total) * 100));
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
}