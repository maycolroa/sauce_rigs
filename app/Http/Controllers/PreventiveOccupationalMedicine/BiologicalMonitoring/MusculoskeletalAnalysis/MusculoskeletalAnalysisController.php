<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisImportJob;
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
         'sau_bm_musculoskeletal_analysis.*',
          DB::raw("CASE WHEN LENGTH(recommendations) > 50 THEN CONCAT(SUBSTRING(recommendations, 1, 50),'...') ELSE recommendations END AS recommendations"),
          DB::raw("CASE WHEN LENGTH(observations) > 50 THEN CONCAT(SUBSTRING(observations, 1, 50),'...') ELSE observations END AS observations"),
          DB::raw("CASE WHEN LENGTH(restrictions) > 50 THEN CONCAT(SUBSTRING(restrictions, 1, 50),'...') ELSE restrictions END AS restrictions"),
          DB::raw("CASE WHEN LENGTH(description_medical_exam) > 50 THEN CONCAT(SUBSTRING(description_medical_exam, 1, 50),'...') ELSE description_medical_exam END AS description_medical_exam"),
          DB::raw("CASE WHEN LENGTH(symptom) > 50 THEN CONCAT(SUBSTRING(symptom, 1, 50),'...') ELSE symptom END AS symptom"),
          DB::raw("CASE WHEN LENGTH(symptomatology_observations) > 50 THEN CONCAT(SUBSTRING(symptomatology_observations, 1, 50),'...') ELSE symptomatology_observations END AS symptomatology_observations")
        );

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
          $data->inConsolidatedPersonalRiskCriterion($this->getValuesForMultiselect($filters["consolidatedPersonalRiskCriterion"]), $filters['filtersType']['consolidatedPersonalRiskCriterion']);
          $data->inBranchOffice($this->getValuesForMultiselect($filters["branchOffice"]), $filters['filtersType']['branchOffice']);
          $data->inCompanies($this->getValuesForMultiselect($filters["companies"]), $filters['filtersType']['companies']);
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
}