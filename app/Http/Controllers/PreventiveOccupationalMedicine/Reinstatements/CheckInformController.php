<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\Reinstatements\InformManagerCheck;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Traits\Filtertrait;

class CheckInformController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_checks_informs, {$this->team}", ['only' => 'data']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    public function dataReport(Request $request)
    {
        $filters = $request->get('filters');

        $checks = Check::select(
            'disease_origin AS tipo_evento',
            'sau_reinc_cie10_codes.code AS cie10',
            'update_cie_11 AS actualizo',
            'sau_reinc_cie11_codes.code AS cie11',
            'sau_employees.name AS name_employee',
            'sau_employees_regionals.name AS regional',
            'sau_employees_headquarters.name AS headquarter',
            'sau_employees_processes.name AS process',
            'sau_employees_areas.name AS area'
        )
        ->leftJoin('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->leftJoin('sau_reinc_cie11_codes', 'sau_reinc_cie11_codes.id', 'sau_reinc_checks.cie11_code_id')
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_employees.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees.employee_area_id')
        ->orderBy('sau_reinc_checks.id', 'DESC');

        if (COUNT($filters) > 0)
        {
            if (isset($filters["cie10"]))
                $checks->inCodCie($this->getValuesForMultiselect($filters["cie10"]), $filters['filtersType']['cie10']);

            if (isset($filters["cie11"]))
                $checks->inCodCie11($this->getValuesForMultiselect($filters["cie11"]), $filters['filtersType']['cie11']);
            
            if (isset($filters["identifications"]))
                $checks->inIdentifications($this->getValuesForMultiselect($filters["identifications"]), $filters['filtersType']['identifications']);

            if (isset($filters["names"]))
                $checks->inNames($this->getValuesForMultiselect($filters["names"]), $filters['filtersType']['names']);

            if (isset($filters["regionals"]))
                $checks->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["businesses"]))
                $checks->inBusinesses($this->getValuesForMultiselect($filters["businesses"]), $filters['filtersType']['businesses']);

            if (isset($filters["diseaseOrigin"]))
                $checks->inDiseaseOrigin($this->getValuesForMultiselect($filters["diseaseOrigin"]), $filters['filtersType']['diseaseOrigin']);
                
            if (isset($filters["years"]))
                $checks->inYears($this->getValuesForMultiselect($filters["years"]), $filters['filtersType']['years']);

            if (isset($filters["nextFollowDays"]))
                $checks->inNextFollowDays($this->getValuesForMultiselect($filters["nextFollowDays"]), $filters['filtersType']['nextFollowDays']);

            if (isset($filters["sveAssociateds"]))
                $checks->inSveAssociateds($this->getValuesForMultiselect($filters["sveAssociateds"]), $filters['filtersType']['sveAssociateds']);

            if (isset($filters["medicalCertificates"]))
                $checks->inMedicalCertificates($this->getValuesForMultiselect($filters["medicalCertificates"]), $filters['filtersType']['medicalCertificates']);

            if (isset($filters["relocatedTypes"]))
                $checks->inRelocatedTypes($this->getValuesForMultiselect($filters["relocatedTypes"]), $filters['filtersType']['relocatedTypes']);

            if (isset($filters["headquarters"]))
                $checks->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $checks->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $checks->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $checks->betweenDate($dates);
        }

        return Vuetable::of($checks)
                    ->make();
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $identifications = $this->getValuesForMultiselect($request->identifications);
        $names = $this->getValuesForMultiselect($request->names);
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $headquarters_filters = $this->getValuesForMultiselect($request->headquarters);
        $processes = $this->getValuesForMultiselect($request->processes);
        $areas = $this->getValuesForMultiselect($request->areas);
        $businesses = $this->getValuesForMultiselect($request->businesses);
        $diseaseOrigin = $this->getValuesForMultiselect($request->diseaseOrigin);
        $codsCie = $this->getValuesForMultiselect($request->cie10);
        $codsCie11 = $this->getValuesForMultiselect($request->cie11);
        $years = $this->getValuesForMultiselect($request->years);
        $nextFollowDays = $request->has('nextFollowDays') ? $this->getValuesForMultiselect($request->nextFollowDays) : null;
        $sveAssociateds = $request->has('sveAssociateds') ? $this->getValuesForMultiselect($request->sveAssociateds) : null;
        $medicalCertificates = $request->has('medicalCertificates') ? $this->getValuesForMultiselect($request->medicalCertificates) : null;
        $relocatedTypes = $request->has('relocatedTypes') ? $this->getValuesForMultiselect($request->relocatedTypes) : null;
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }
        
        $informManager = new InformManagerCheck($identifications, $names, $regionals, $businesses, $diseaseOrigin, $nextFollowDays, $dates, $years, $sveAssociateds, $medicalCertificates, $relocatedTypes, $filtersType, $this->company, $codsCie, $headquarters_filters, $processes, $areas, $codsCie11);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}