<?php

namespace App\Inform\PreventiveOccupationalMedicine\Reinstatements;

use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Traits\ConfigurableFormTrait;

class InformManagerCheck
{
    use ConfigurableFormTrait;

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
        'headers',
        'open_reports_bar_year',
        'open_reports_bar_month',
        'closed_reports_bar_year',
        'closed_reports_bar_month',
        'disease_origin_reports_pie',
        'cases_per_regional_pie',
        'cases_per_headquarter_pie',
        'cases_per_business_pie',
        'cases_per_sve_associateds_pie',
        'cases_per_medical_certificates_pie',
        'cases_per_cie_10_per_EG_pie',
        'cases_per_cie_10_per_EL_pie',
        'cases_per_cie_10_per_AT_pie'
    ];

    /**
     * this array must contain only the identifiers according to the case of the filter
     * @var array
     */
    protected $identifications;
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

    /**
     * create an instance and set the attribute class
     * @param array $identifications
     */
    function __construct($identifications = [], $names = [], $regionals = [], $businesses = [], $diseaseOrigin = [], $nextFollowDays = [], $dateRange = [], $years = [], $sveAssociateds = [], $medicalCertificates = [], $relocatedTypes = [], $filtersType = [])
    {
        $this->identifications = $identifications;
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
     * Return the total amount of exposed population
     *
     * @return void
     */
    private function getTotalChecks()
    {
        $totalChecks = Check::countDistinctEmployeeId()
        ->isOpen()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange);

        if ($this->nextFollowDays)
            $totalChecks->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $totalChecks->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $totalChecks->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $totalChecks->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $totalChecks = $totalChecks->secureCount();

        return $totalChecks;
    }

    private function executeQueryHeader($condition)
    {
        $data = Check::countDistinctEmployeeId()
        ->where($condition)
        ->isOpen()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange);

        if ($this->nextFollowDays)
            $data->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);
            
        if ($this->sveAssociateds)
            $data->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $data->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $data->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $data = $data->secureCount();

        return $data;
    }

    /**
     * return the headers reports pie data for the view
     * @return object
     */
    public function headers()
    {        
        $result = [
            'checksWithRecommendations' => [
                'label' => 'Porcentaje reportes con recomendaciones',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['has_recommendations', 'SI']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksWithIndefiniteRecommendations' => [
                'label' => 'Porcentaje reportes con recomendaciones indefinidas',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['has_recommendations', 'SI'],
                        ['indefinite_recommendations', 'SI']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksWithRelocated' => [
                'label' => 'Porcentaje reportes con reubicación laboral',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['has_recommendations', 'SI'],
                        ['relocated', 'SI']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksInProcessOrigin' => [
                'label' => 'Número de reportes en proceso calificación de origen',
                'value' => $this->executeQueryHeader([
                        ['in_process_origin', 'SI']
                    ])
            ],
            'checksInProcessPcl' => [
                'label' => 'Número de reportes en proceso calificación de pérdida',
                'value' => $this->executeQueryHeader([
                    ['in_process_pcl', 'SI']
                ])
            ],
            'checksWithoutRecommendations' => [
                'label' => 'Porcentaje reportes sin recomendaciones',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['has_recommendations', 'NO']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksWithoutRestrictions' => [
                'label' => 'Porcentaje reportes sin restricciones',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['has_restrictions', 'NO']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksDiseaseOrigin' => [
                'label' => 'Porcentaje reportes por enfermedad laboral',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['disease_origin', 'Enfermedad Laboral']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedARLOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por ARL',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'ARL']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedEPSOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por EPS',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'EPS']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedAFPOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por AFP',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'AFP']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedRegionalBoardOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por Junta Regional',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'JUNTA REGIONAL']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedNationalBoardOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por Junta Nacional',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'JUNTA NACIONAL']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ],
            'checksQualifiedOtherEntitiesOrigin' => [
                'label' => 'Porcentaje reportes calificados por Origen por Otras Entidades',
                'value' => $this->percentage(
                    $this->executeQueryHeader([
                        ['emitter_origin', 'Sin Información']
                    ])
                    , $this->totalChecks),
                'type' => 'percentage'
            ]
        ];

        if ($this->formModel == 'misionEmpresarial')
        {
            $result = array_merge($result, 
                [
                    'checksQualifiedArlPcl' => [
                        'label' => 'Porcentaje reportes calificados por PCL por ARL',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'ARL']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ],
                    'checksQualifiedEpsPcl' => [
                        'label' => 'Porcentaje reportes calificados por PCL por EPS',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'EPS']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ],
                    'checksQualifiedAfpPcl' => [
                        'label' => 'Porcentaje reportes calificados por PCL por AFP',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'AFP']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ],
                    'checksQualifiedRegionalBoardPCL' => [
                        'label' => 'Porcentaje reportes calificados por PCL por Junta Regional',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'JUNTA REGIONAL']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ],
                    'checksQualifiedNationalBoardPCL' => [
                        'label' => 'Porcentaje reportes calificados por PCL por Junta Nacional',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'JUNTA NACIONAL']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ],
                    'checksQualifiedOtherEntitiesPCL' => [
                        'label' => 'Porcentaje reportes calificados por PCL por Otras Entidades',
                        'value' => $this->percentage(
                            $this->executeQueryHeader([
                                ['entity_rating_pcl', 'Sin Información']
                            ])
                            , $this->totalChecks),
                        'type' => 'percentage'
                    ]
                ]
            );
        }

        return $result;
    }

    /**
     * Returns the data in the open report bar by month for the view.
     * @return collection
     */
    public function open_reports_bar_month()
    {
        return $this->reports_bar_month(true);
    }

    /**
     * Returns the data in the open report bar by month for the view.
     * @return collection
     */
    public function closed_reports_bar_month()
    {
        return $this->reports_bar_month(false);
    }

    /**
     * return the open reports bar data for the view
     * @return collection
     */
    public function reports_bar_month($isOpen)
    {
        $checksPerMonth = Check::selectRaw("
            MONTH(sau_reinc_checks.created_at) AS month,
            COUNT(DISTINCT employee_id) AS count_per_month
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->isOpen($isOpen)
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->groupBy('month');

        if ($this->nextFollowDays)
            $checksPerMonth->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $checksPerMonth->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $checksPerMonth->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $checksPerMonth->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $checksPerMonth = $checksPerMonth->pluck('count_per_month', 'month');

        $months = [];
        $data = [];
        $total = 0;

        for ($i = 1; $i <= 12; $i++)
        {
            array_push($months, trans("months.$i"));
            $value = isset($checksPerMonth[$i]) ? $checksPerMonth[$i] : 0;
            array_push($data, $value);
            $total += $value;
        }

        return collect([
            'labels' => $months,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
    }

    /**
     * Returns the data in the open report bar by year for the view.
     * @return collection
     */
    public function open_reports_bar_year()
    {
        return $this->reports_bar_year(true);
    }

    /**
     * Returns the data in the open report bar by year for the view.
     * @return collection
     */
    public function closed_reports_bar_year()
    {
        return $this->reports_bar_year(false);
    }

    private function reports_bar_year($isOpen)
    {
        $checksPerYear = Check::selectRaw("
            YEAR(sau_reinc_checks.created_at) AS year,
            COUNT(DISTINCT employee_id) AS count_per_year
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->isOpen($isOpen)
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->groupBy('year');

        if ($this->nextFollowDays)
            $checksPerYear->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $checksPerYear->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $checksPerYear->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $checksPerYear->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $checksPerYear = $checksPerYear->pluck('count_per_year', 'year');

        return $this->buildDataChart($checksPerYear);
    }

    /**
     * return the origin disease reports pie data for the view
     * @return collection
     */
    public function disease_origin_reports_pie()
    {
        return $this->getReportPerColumn('disease_origin');
    }

    /**
     * return the origin disease reports pie data for the view
     * @return collection
     */
    public function cases_per_sve_associateds_pie()
    {
        return $this->getReportPerColumn('sve_associated');
    }

    /**
     * return the origin disease reports pie data for the view
     * @return collection
     */
    public function cases_per_medical_certificates_pie()
    {
        return $this->getReportPerColumn('medical_certificate_ueac');
    }

    /**
     * Returns the reports of pta for column.
     * @return collection
     */
    private function getReportPerColumn($column)
    {
        $data = Check::selectRaw(
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

        return $this->buildDataChart($data);
    }

    /**
     * Returns the reports of left air pta.
     * @return collection
     */
    public function cases_per_cie_10_per_EG_pie()
    {
        return $this->getReportPerCie10Data('Enfermedad General');
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function cases_per_cie_10_per_EL_pie()
    {
        return $this->getReportPerCie10Data('Enfermedad Laboral');
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function cases_per_cie_10_per_AT_pie()
    {
        return $this->getReportPerCie10Data('Accidente de Trabajo');
    }

    /**
     * returns the data for the cases per cie 10 code category according
     * to the specified disease_origin
     * @param  string $disease_origin
     * @return collection
     */
    public function getReportPerCie10Data($disease_origin)
    {
        $checksPerCie10Code = Check::selectRaw("
            sau_reinc_cie10_codes.category AS cie10_code_category,
            COUNT(DISTINCT employee_id) AS count_per_cie10_code
        ")
        ->isOpen()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', '=', 'sau_reinc_checks.cie10_code_id')
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->where('sau_reinc_checks.disease_origin', $disease_origin)
        ->groupBy('sau_reinc_cie10_codes.category');

        if ($this->nextFollowDays)
            $checksPerCie10Code->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $checksPerCie10Code->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $checksPerCie10Code->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $checksPerCie10Code->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $checksPerCie10Code = $checksPerCie10Code->pluck('count_per_cie10_code', 'cie10_code_category');

        return $this->buildDataChart($checksPerCie10Code);
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function cases_per_regional_pie()
    {
        return $this->cases_per_column_employee_pie('sau_employees_regionals', 'employee_regional_id');
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function cases_per_headquarter_pie()
    {
        return $this->cases_per_column_employee_pie('sau_employees_headquarters', 'employee_headquarter_id');
    }

    /**
     * Returns the reports of right air pta.
     * @return collection
     */
    public function cases_per_business_pie()
    {
        return $this->cases_per_column_employee_pie('sau_employees_businesses', 'employee_business_id');
    }

    /**
     * returns the cases per regional pie data for the view
     * @return collection
     */
    private function cases_per_column_employee_pie($table, $column)
    {
        $checksPerColumn = Check::selectRaw($table.".name AS name,
            COUNT(DISTINCT sau_employees.id) AS count
        ")
        ->isOpen()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->join($table, $table.'.id', 'sau_employees.'.$column)
        ->inIdentifications($this->identifications, $this->filtersType['identifications'])
        ->inNames($this->names, $this->filtersType['names'])
        ->inRegionals($this->regionals, $this->filtersType['regionals'])
        ->inBusinesses($this->businesses, $this->filtersType['businesses'])
        ->inDiseaseOrigin($this->diseaseOrigin, $this->filtersType['diseaseOrigin'])
        ->inYears($this->years, $this->filtersType['years'])
        ->betweenDate($this->dateRange)
        ->groupBy($table.'.name');

        if ($this->nextFollowDays)
            $checksPerColumn->inNextFollowDays($this->nextFollowDays, $this->filtersType['nextFollowDays']);

        if ($this->sveAssociateds)
            $checksPerColumn->inSveAssociateds($this->sveAssociateds, $this->filtersType['sveAssociateds']);

        if ($this->medicalCertificates)
            $checksPerColumn->inMedicalCertificates($this->medicalCertificates, $this->filtersType['medicalCertificates']);

        if ($this->relocatedTypes)
            $checksPerColumn->inRelocatedTypes($this->relocatedTypes, $this->filtersType['relocatedTypes']);

        $checksPerColumn = $checksPerColumn->pluck('count', 'name');

        return $this->buildDataChart($checksPerColumn);
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
        return ($value / $totalValue) * 100;
    }
}