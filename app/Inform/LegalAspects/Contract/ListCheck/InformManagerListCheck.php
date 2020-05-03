<?php

namespace App\Inform\LegalAspects\Contract\ListCheck;

use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ListCheckResumen;

class InformManagerListCheck
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
        'contracts',
        'standar'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $company_id;
    /*protected $identifications;
    protected $names;
    protected $regionals;
    protected $businesses;
    protected $diseaseOrigin;
    protected $nextFollowDays;
    protected $dateRange;
    protected $years;
    protected $sveAssociateds;
    protected $medicalCertificates;
    protected $relocatedTypes;
    protected $filtersType;
    protected $formModel;
    protected $totalChecks;
    protected $locationForm;*/

    /**
     * create an instance and set the attribute class
     * @param array $identifications
     */
    function __construct($company_id/*$identifications = [], $names = [], $regionals = [], $businesses = [], $diseaseOrigin = [], $nextFollowDays = [], $dateRange = [], $years = [], $sveAssociateds = [], $medicalCertificates = [], $relocatedTypes = [], $filtersType = []*/)
    {
        $this->company_id = $company_id;
        /*$this->identifications = $identifications;
        $this->names = $names;
        $this->regionals = $regionals;
        $this->businesses = $businesses;
        $this->diseaseOrigin = $diseaseOrigin;
        $this->nextFollowDays = $nextFollowDays;
        $this->dateRange = $dateRange;
        $this->years = $years;
        $this->sveAssociateds = $sveAssociateds;
        $this->medicalCertificates = $medicalCertificates;
        $this->relocatedTypes = $relocatedTypes;
        $this->filtersType = $filtersType;
        $this->formModel = $this->getFormModel('form_check');
        $this->totalChecks = $this->getTotalChecks();
        $this->locationForm = $this->getLocationFormConfModule();*/
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
                WHEN total_p_c BETWEEN 0 AND 35 THEN '0% - 35%'
                WHEN total_p_c BETWEEN 36 AND 70 THEN '36% - 70%'
                WHEN total_p_c > 7 THEN '71% - 100%'
                ELSE '0% - 35%' END AS label,
            CASE
                WHEN total_p_c BETWEEN 0 AND 35 THEN 1
                WHEN total_p_c BETWEEN 36 AND 70 THEN 2
                WHEN total_p_c > 71 THEN 3
                ELSE 1 END AS orden,
            COUNT(*) AS total
        ")
        ->leftJoin('sau_ct_list_check_resumen', 'sau_ct_list_check_resumen.contract_id', 'sau_ct_information_contract_lessee.id')
        ->where('sau_ct_information_contract_lessee.type', 'Contratista')
        ->groupBy('label', 'orden')
        ->orderBy('orden', 'DESC')
        ->pluck('total', 'label');

        \Log::info($contracts);

        return $this->buildDataChart($contracts);
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function standar()
    {
        /*$data = Check::selectRaw(
            $column." AS ".$column.",
            COUNT(DISTINCT employee_id) AS count
        ")
        ->isOpen()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->where($column, '<>', '')
        ->groupBy($column);

        if ($this->nextFollowDays)
            $data->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $data->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $data->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $data->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $data = $data->pluck('count', $column);

        return $this->buildTableChartData($data);*/
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

    private function buildTableChartData($data)
    {
        $result = collect([]);
        $result->put('chart', $this->buildDataChart($data));

        return $result;
    }

    /**
     * computes the percentage of a value related to a total value
     * @param  integer|float $value
     * @param  integer|float $totalValue
     * @return integer|float
     */
    protected function percentage($value, $totalValue)
    {
        if ($totalValue == 0) {
            return 'N/A';
        }

        return round(($value / $totalValue) * 100, 1);
    }
}