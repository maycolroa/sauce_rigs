<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisImportJob;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisExportJob;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use DB;

class MusculoskeletalAnalysisController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_c', ['only' => ['store', 'import', 'downloadTemplateImport']]);
        $this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_r');
        //$this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_u', ['only' => 'update']);
        //$this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_d', ['only' => 'destroy']);
    }

    /**
     * Display index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function data(Request $request)
   {
       $data = MusculoskeletalAnalysis::select(
         'sau_bm_musculoskeletal_analysis.*'
        );

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
          $data->inConsolidatedPersonalRiskCriterion($this->getValuesForMultiselect($filters["consolidatedPersonalRiskCriterion"]), $filters['filtersType']['consolidatedPersonalRiskCriterion']);
          $data->inBranchOffice($this->getValuesForMultiselect($filters["branchOffice"]), $filters['filtersType']['branchOffice']);
          $data->inCompanies($this->getValuesForMultiselect($filters["companies"]), $filters['filtersType']['companies']);

          $dates_request = explode('/', $filters["dateRange"]);
          $dates = [];

          if (COUNT($dates_request) == 2)
          {
            array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d'));
          }
            
          $data->betweenDate($dates);
        }

        return Vuetable::of($data)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AudiometryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AudiometryRequest $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AudiometryRequest $request, Audiometry $audiometry)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audiometry $audiometry)
    {
        
    }

    /**
     * import.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try
      {
        MusculoskeletalAnalysisImportJob::dispatch($request->file, Session::get('company_id'), Auth::user());
      
        return $this->respondHttp200();
      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectConsolidatedPersonalRiskCriterion()
    {
      $data = MusculoskeletalAnalysis::selectRaw(
        'DISTINCT consolidated_personal_risk_criterion AS consolidated_personal_risk_criterion'
      )
      ->orderBy('consolidated_personal_risk_criterion')
      ->get()
      ->pluck('consolidated_personal_risk_criterion', 'consolidated_personal_risk_criterion');

      return $this->multiSelectFormat($data);
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectBranchOffice()
    {
      $data = MusculoskeletalAnalysis::selectRaw(
        'DISTINCT branch_office AS branch_office'
      )
      ->orderBy('branch_office')
      ->get()
      ->pluck('branch_office', 'branch_office');

      return $this->multiSelectFormat($data);
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectCompany()
    {
      $data = MusculoskeletalAnalysis::selectRaw(
        'DISTINCT company AS company'
      )
      ->orderBy('company')
      ->get()
      ->pluck('company', 'company');

      return $this->multiSelectFormat($data);
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
      try
      {
        $consolidatedPersonalRiskCriterion = $this->getValuesForMultiselect($request->consolidatedPersonalRiskCriterion);
        $branchOffice = $this->getValuesForMultiselect($request->branchOffice);
        $companies = $this->getValuesForMultiselect($request->companies);
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }

        $filters = [
            'consolidatedPersonalRiskCriterion' => $consolidatedPersonalRiskCriterion,
            'branchOffice' => $branchOffice,
            'companies' => $companies,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        MusculoskeletalAnalysisExportJob::dispatch(Auth::user(), Session::get('company_id'), $filters);
      
        return $this->respondHttp200();
      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }
}