<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\InformManagerMusculoskeletalAnalysis;

class MusculoskeletalAnalysisInformController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_r', ['only' => 'data']);
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
        
        $informManager = new InformManagerMusculoskeletalAnalysis($consolidatedPersonalRiskCriterion, $branchOffice, $companies, $dates, $filtersType);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
