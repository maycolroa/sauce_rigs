<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\InformManagerMusculoskeletalAnalysis;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\InformIndividualManagerMusculoskeletalAnalysis;
use App\Traits\Filtertrait;

class MusculoskeletalAnalysisInformController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:biologicalMonitoring_musculoskeletalAnalysis_r, {$this->team}", ['only' => 'data']);
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
        $url = "/preventiveoccupationalmedicine/biologicalmonitoring/musculoskeletalanalysis/informs";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $consolidatedPersonalRiskCriterion = !$init ? $this->getValuesForMultiselect($request->consolidatedPersonalRiskCriterion) : (isset($filters['consolidatedPersonalRiskCriterion']) ? $this->getValuesForMultiselect($filters['consolidatedPersonalRiskCriterion']) : []);
        $branchOffice = !$init ? $this->getValuesForMultiselect($request->branchOffice) : (isset($filters['branchOffice']) ? $this->getValuesForMultiselect($filters['branchOffice']) : []);
        $companies = !$init ? $this->getValuesForMultiselect($request->companies) : (isset($filters['companies']) ? $this->getValuesForMultiselect($filters['companies']) : []);
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $dates = [];

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);
        
        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Ymd'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Ymd'));
            }
            
        }
        
        $informManager = new InformManagerMusculoskeletalAnalysis($consolidatedPersonalRiskCriterion, $branchOffice, $companies, $dates, $filtersType);
        
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
        
        $informManager = new InformIndividualManagerMusculoskeletalAnalysis($id);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
