<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Models\Administrative\Users\User;
//use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\Tracing;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisImportJob;
//use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisExportJob;
//use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\InformIndividualManagerMusculoskeletalAnalysis;
use Carbon\Carbon;
use DB;

class RespiratoryAnalysisController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        //$this->middleware('permission:biologicalMonitoring_musculoskeletalAnalysis_c', ['only' => ['store', 'import', 'downloadTemplateImport']]);
        //$this->middleware("permission:biologicalMonitoring_musculoskeletalAnalysis_r, {$this->team}");
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
       $data = RespiratoryAnalysis::select(
         'sau_bm_respiratory_analysis.*'
        );

        //$filters = $request->get('filters');

        /*if (COUNT($filters) > 0)
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
        }*/

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
      $respiratoryAnalysis = RespiratoryAnalysis::findOrFail($id);

      try
      { 
        return $this->respondHttp200([
          'data' => $respiratoryAnalysis,
        ]);
        
      } catch(Exception $e) {
        $this->respondHttp500();
      }
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
        RespiratoryAnalysisImportJob::dispatch($request->file, $this->company, $this->user);
      
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
    /*public function multiselectConsolidatedPersonalRiskCriterion()
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
    /*public function multiselectBranchOffice()
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
    /*public function multiselectCompany()
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
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectPacient(Request $request)
    {
      if($request->has('keyword'))
      {
        $keyword = "%{$request->keyword}%";
        $data = RespiratoryAnalysis::select(
          DB::raw('DISTINCT CAST(patient_identification AS SIGNED) AS id'),
          DB::raw('CONCAT(patient_identification, " - ", name) AS name')
          )
          ->where(function ($query) use ($keyword) {
              $query->orWhere('name', 'like', $keyword);
              $query->orWhere('patient_identification', 'like', $keyword);
          })
          ->take(30)->orderBy('id')->pluck('id', 'name');

        return $this->respondHttp200([
            'options' => $this->multiSelectFormat($data)
        ]);
      }
      else
      {
        $data = RespiratoryAnalysis::select(
          DB::raw('DISTINCT CAST(patient_identification AS SIGNED) AS id'),
          'name'
        )
        ->orderBy('id')
        ->pluck('name', 'id');
  
        return $this->multiSelectFormat($data);
      }
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function export(Request $request)
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

        MusculoskeletalAnalysisExportJob::dispatch($this->user, $this->company, $filters);
      
        return $this->respondHttp200();
      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }

    public function saveTracing(Request $request)
    {
      try
      {
        DB::beginTransaction();

        if (!$this->saveTracingPrivate($request->identification, $request->new_tracing, $this->user, $request->oldTracings))
          return $this->respondHttp500();

        DB::commit();

      } catch (Exception $e){
          DB::rollback();
          return $this->respondHttp500();
      }

      $informManager = new InformIndividualManagerMusculoskeletalAnalysis($request->identification);  
      return $this->respondHttp200($informManager->getInformData(['oldTracings']));
    }

    private function saveTracingPrivate($identification, $tracingDescription, User $madeByUser, $tracingsToUpdate = [])
    {
        try
        {
            $this->handleTracingUpdates($madeByUser, $tracingsToUpdate);

            if (!$tracingDescription)
                return true;

            $tracing = new Tracing();
            $tracing->company_id = $this->company;
            $tracing->description = $tracingDescription;
            $tracing->identification = $identification;
            $tracing->user_id = $madeByUser->id;

            return $tracing->save();

        } catch (Exception $e) {
            \Log::error("{$e->getMessage()} \n {$e->getTraceAsString()}");
            return false;
        }
    }

    /**
     * updates old tracings if necessary
     * @param  array  $tracingsToUpdate
     * @return void
     */
    /*public function handleTracingUpdates(User $madeByUser, $tracingsToUpdate = [])
    {
        if (!is_array($tracingsToUpdate))
            return;

        foreach ($tracingsToUpdate as $tracing)
        {
            $oldTracing = Tracing::where('id', $tracing["id"])->first();

            if (!$oldTracing)
                continue;

            if ($tracing["description"] != $oldTracing->description)  
            {
                $oldTracing->update([
                    'description' => $tracing["description"],
                    'user_id' => $madeByUser->id
                ]);
            }
        }
    }*/
}