<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryRequest;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Carbon\Carbon;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryExportJob;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportJob;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportTemplate;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Traits\AudiometryTrait;

class AudiometryController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:biologicalMonitoring_audiometry_c', ['only' => ['store', 'import', 'downloadTemplateImport']]);
        $this->middleware('permission:biologicalMonitoring_audiometry_r');
        $this->middleware('permission:biologicalMonitoring_audiometry_u', ['only' => 'update']);
        $this->middleware('permission:biologicalMonitoring_audiometry_d', ['only' => 'destroy']);
    }

    use AudiometryTrait;
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
       $audiometry = Audiometry::select(
           'sau_bm_audiometries.*',
           'sau_employees.identification as identification',
           'sau_employees.name as name'
        )->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        /*->join('sau_employees_regionals','sau_employees_regionals.id','sau_employees.employee_regional_id')*/;
        
        if ($request->has('modelId') && $request->get('modelId'))
        {
          $audiometry->where('sau_bm_audiometries.employee_id', '=', $request->get('modelId'));
        }

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
          $audiometry->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);
          $audiometry->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
          $audiometry->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
          $audiometry->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
          $audiometry->inPositions($this->getValuesForMultiselect($filters["positions"]), $filters['filtersType']['positions']);
          $audiometry->inDeals($this->getValuesForMultiselect($filters["deals"]), $filters['filtersType']['deals']);
          $audiometry->inYears($this->getValuesForMultiselect($filters["years"]), $filters['filtersType']['years']);
          $audiometry->inRegionals($this->getValuesForMultiselect($filters["regionalsHeader"]), $filters['filtersType']['regionalsHeader']);
          $audiometry->inNames($this->getValuesForMultiselect($filters["names"]), $filters['filtersType']['names']);
          $audiometry->inIdentifications($this->getValuesForMultiselect($filters["identifications"]), $filters['filtersType']['identifications']);
          $audiometry->inSeverityGradeLeft($this->getValuesForMultiselect($filters["severity_grade_left"]), $filters['filtersType']['severity_grade_left']);
          $audiometry->inSeverityGradeRight($this->getValuesForMultiselect($filters["severity_grade_right"]), $filters['filtersType']['severity_grade_right']);
          //$audiometry->inBusinesses($this->getValuesForMultiselect($filters["businesses"]), $filters['filtersType']['businesses']);

          $dates_request = explode('/', $filters["dateRange"]);
          $dates = [];

          if (COUNT($dates_request) == 2)
          {
            array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Ymd'));
            array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Ymd'));
          }
            
          $audiometry->betweenDate($dates);
        }

       return Vuetable::of($audiometry)
                ->addColumn('base_si_no', function ($audiometry) {
                  return $audiometry->base_type == 'Base' ? 'Si' : 'No';
                })
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
        $audiometry = new Audiometry($request->all());
        $audiometry->date = (Carbon::createFromFormat('D M d Y',$audiometry->date))->format('Ymd');
        
        if(!$audiometry->save()){
            return $this->respondHttp500();
        }

        $this->calculateBaseAudiometry($audiometry->employee_id);

        return $this->respondHttp200([
            'message' => 'Se creo la audiometria'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $audiometry = Audiometry::findOrFail($id);

      try{
        $audiometry->date = (Carbon::createFromFormat('Y-m-d',$audiometry->date))->format('D M d Y');
        $audiometry->multiselect_employee = $audiometry->employee->multiselect(); 
        return $this->respondHttp200([
            'data' => $audiometry,
        ]);
      }catch(Exception $e){
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
 
      $audiometry->fill($request->all());
      $audiometry->date = (Carbon::createFromFormat('D M d Y',$audiometry->date))->format('Ymd');
      
      if(!$audiometry->update()){
        return $this->respondHttp500();
      }

      $this->calculateBaseAudiometry($audiometry->employee_id);

      return $this->respondHttp200([
          'message' => 'Se actualizo la audiometria'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audiometry $audiometry)
    {
        $employee_id = $audiometry->employee_id;

        if(!$audiometry->delete()){
          return $this->respondHttp500();
        }

        $this->calculateBaseAudiometry($employee_id);
        
        return $this->respondHttp200([
            'message' => 'Se elimino la audiometria'
        ]);
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
      try{
        AudiometryExportJob::dispatch(Auth::user(), Session::get('company_id'));
      
        return $this->respondHttp200();
      }catch(Exception $e){
        return $this->respondHttp500();
      }

      

      
    }

    /**
     * import.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try{
       AudiometryImportJob::dispatch($request->file, Session::get('company_id'), Auth::user());
      
       return $this->respondHttp200();
      }catch(Exception $e){
        return $this->respondHttp500();
      }
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectYears()
    {
      $audiometries = Audiometry::selectRaw(
        'DISTINCT YEAR(sau_bm_audiometries.date) as year'
      )->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
      ->orderBy('year')
      ->get()
      ->pluck('year', 'year');

      return $this->multiSelectFormat($audiometries);
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new AudiometryImportTemplate, 'PlantillaImportacionAudiometria.xlsx');
    }

    /**
     * Returns an arrangement with the severity_grade_air_left_pta
     *
     * @return Array
     */
    public function multiselectSeverityGradeLeft()
    {
      $data = Audiometry::selectRaw(
              "DISTINCT sau_bm_audiometries.severity_grade_air_left_pta AS severity_grade_air_left_pta"
          )
          ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
          ->whereNotNull('sau_bm_audiometries.severity_grade_air_left_pta')
          ->pluck('severity_grade_air_left_pta', 'severity_grade_air_left_pta');

      return $this->multiSelectFormat($data);
    }

    /**
     * Returns an arrangement with the severity_grade_air_right_pta
     *
     * @return Array
     */
    public function multiselectSeverityGradeRight()
    {
      $data = Audiometry::selectRaw(
              "DISTINCT sau_bm_audiometries.severity_grade_air_right_pta AS severity_grade_air_right_pta"
          )
          ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
          ->whereNotNull('sau_bm_audiometries.severity_grade_air_right_pta')
          ->pluck('severity_grade_air_right_pta', 'severity_grade_air_right_pta');

      return $this->multiSelectFormat($data);
    }
}