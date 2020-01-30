<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\InformManagerRespiratoryAnalysis;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\InformIndividualManagerRespiratoryAnalysis;

class RespiratoryAnalysisInformController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:biologicalMonitoring_respiratoryAnalysis_r, {$this->team}", ['only' => 'data']);
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

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $consolidatedPersonalRiskCriterion = $this->getValuesForMultiselect($request->consolidatedPersonalRiskCriterion);
        $branchOffice = $this->getValuesForMultiselect($request->branchOffice);
        $companies = $this->getValuesForMultiselect($request->companies);
        $filtersType = $request->filtersType;

        $dates = [];
        
        if (isset($request->dateRange) && $request->dateRange)
        {
            $dates_request = explode('/', $request->dateRange);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Ymd'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Ymd'));
            }
            
        }
        
        $informManager = new InformManagerRespiratoryAnalysis($consolidatedPersonalRiskCriterion, $branchOffice, $companies, $dates, $filtersType);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function dataIndividual(Request $request)
    {
        $id = $request->patient_identification;
        
        $informManager = new InformIndividualManagerRespiratoryAnalysis($id);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
