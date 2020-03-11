<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\InformManagerRespiratoryAnalysis;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\InformIndividualManagerRespiratoryAnalysis;
use App\Traits\Filtertrait;

class RespiratoryAnalysisInformController extends Controller
{
    use Filtertrait;
    
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
        $url = "/preventiveoccupationalmedicine/biologicalmonitoring/respiratoryanalysis/informs";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $regional = !$init ? $this->getValuesForMultiselect($request->regional) : (isset($filters['regional']) ? $this->getValuesForMultiselect($filters['regional']) : []);

        $deal = !$init ? $this->getValuesForMultiselect($request->deal) : (isset($filters['deal']) ? $this->getValuesForMultiselect($filters['deal']) : []);
        
        $interpretation = !$init ? $this->getValuesForMultiselect($request->interpretation) : (isset($filters['interpretation']) ? $this->getValuesForMultiselect($filters['interpretation']) : []);
        
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
        
        $informManager = new InformManagerRespiratoryAnalysis($regional, $deal, $interpretation, $dates, $filtersType);
        
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
