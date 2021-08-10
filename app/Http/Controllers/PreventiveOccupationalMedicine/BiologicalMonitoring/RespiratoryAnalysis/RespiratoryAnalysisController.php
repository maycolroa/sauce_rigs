<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Models\Administrative\Users\User;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\TracingRespiratoryAnalysis;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisImportJob;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisExportJob;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\InformIndividualManagerRespiratoryAnalysis;
use Carbon\Carbon;
use App\Traits\Filtertrait;
use DB;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisTemplateExcel;
use Maatwebsite\Excel\Facades\Excel;

class RespiratoryAnalysisController extends Controller
{
    use Filtertrait;

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
       
       $url = "/preventiveoccupationalmedicine/biologicalmonitoring/respiratoryanalysis";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
          $data->inRegional($this->getValuesForMultiselect($filters["regional"]), $filters['filtersType']['regional']);

          if (isset($filters["deal"]) && isset($filters['filtersType']['deal']))
            $data->inDeal($this->getValuesForMultiselect($filters["deal"]), $filters['filtersType']['deal']);
          if (isset($filters["interpretation"]) && isset($filters['filtersType']['interpretation']))
            $data->inInterpretation($this->getValuesForMultiselect($filters["interpretation"]), $filters['filtersType']['interpretation']);

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
    public function multiselectDeal()
    {
      $data = RespiratoryAnalysis::selectRaw(
        'DISTINCT deal AS deal'
      )
      ->orderBy('deal')
      ->get()
      ->pluck('deal', 'deal');

      return $this->multiSelectFormat($data);
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectRegional()
    {
      $data = RespiratoryAnalysis::selectRaw(
        'DISTINCT regional AS regional'
      )
      ->orderBy('regional')
      ->pluck('regional', 'regional');

      return $this->multiSelectFormat($data);
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectInterpretation()
    {
      $data = RespiratoryAnalysis::selectRaw(
        'DISTINCT interpretation AS interpretation'
      )
      ->orderBy('interpretation')
      ->get()
      ->pluck('interpretation', 'interpretation');

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
    public function export(Request $request)
    {
      try
      {
        $regional = $this->getValuesForMultiselect($request->regional);
        $filtersType = $request->filtersType;

        $filters = [
            'regional' => $regional,
            'filtersType' => $filtersType
        ];

        RespiratoryAnalysisExportJob::dispatch($this->user, $this->company, $filters);
      
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

      $informManager = new InformIndividualManagerRespiratoryAnalysis($request->identification);  
      return $this->respondHttp200($informManager->getInformData(['oldTracings']));
    }

    private function saveTracingPrivate($identification, $tracingDescription, User $madeByUser, $tracingsToUpdate = [])
    {
        try
        {
            $this->handleTracingUpdates($madeByUser, $tracingsToUpdate);

            if (!$tracingDescription)
                return true;

            $tracing = new TracingRespiratoryAnalysis();
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
    public function handleTracingUpdates(User $madeByUser, $tracingsToUpdate = [])
    {
        if (!is_array($tracingsToUpdate))
            return;

        foreach ($tracingsToUpdate as $tracing)
        {
            $oldTracing = TracingRespiratoryAnalysis::where('id', $tracing["id"])->first();

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
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new RespiratoryAnalysisTemplateExcel($this->company, collect([])), 'PlantillaImportacionAnalisisRespiratorio.xlsx');
    }
}