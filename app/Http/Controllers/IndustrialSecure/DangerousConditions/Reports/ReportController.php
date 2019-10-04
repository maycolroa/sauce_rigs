<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Reports\ReportRequest;
use App\Jobs\IndustrialSecure\DangerousConditions\Reports\ReportExportJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;

class ReportController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ph_reports_c', ['only' => 'store']);
        $this->middleware('permission:ph_reports_r');
        $this->middleware('permission:ph_reports_u', ['only' => 'update']);
        $this->middleware('permission:ph_reports_d', ['only' => 'destroy']);
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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $reports = Report::select(
            'sau_ph_reports.id',
            'sau_employees_headquarters.name as headquarter',
            'sau_ph_reports.created_at',
            'sau_users.name as user',
            'sau_ph_conditions.description as condition',
            'sau_ph_conditions_types.description as type',
            'sau_ph_reports.rate'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ph_reports.user_id')
        ->join('sau_ph_conditions', 'sau_ph_conditions.id', 'sau_ph_reports.condition_id')
        ->join('sau_ph_conditions_types', 'sau_ph_conditions_types.id', 'sau_ph_conditions.condition_type_id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id');

        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $inspections->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
            $inspections->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $inspections->betweenDate($dates);
        }*/

        return Vuetable::of($reports)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReportRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $report = new Report($request->all());
            $report->company_id = Session::get('company_id');
            $report->user_id = Auth::user()->id;
            
            if (!$report->save())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el reporte'
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
        try
        {
            $report = Report::findOrFail($id);

            $report->multiselect_regional = $report->regional ? $report->regional->multiselect() : []; 
            $report->multiselect_sede = $report->headquarter ? $report->headquarter->multiselect() : []; 
            $report->multiselect_proceso = $report->process ? $report->process->multiselect() : []; 
            $report->multiselect_area = $report->area ? $report->area->multiselect() : [];
            $report->multiselect_condition = $report->condition ? $report->condition->multiselect() : [];

            return $this->respondHttp200([
                'data' => $report,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @param  Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function update(ReportRequest $request, Report $report)
    {
        DB::beginTransaction();

        try
        {
            $report->fill($request->all());
            
            if (!$report->update())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el reporte'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Inspection $inspection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        if(!$report->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el reporte'
        ]);
    }

    public function export(Request $request)
    {
      try
      {
        $headquarters = $this->getValuesForMultiselect($request->headquarters);
        $areas = $this->getValuesForMultiselect($request->areas);
        //$names = $this->getValuesForMultiselect($request->names);
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }

        $filters = [
            'headquarters' => $headquarters,
            'areas' => $areas,
            //'names' => $names,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        InspectionExportJob::dispatch(Auth::user(), Session::get('company_id'), $filters);
      
        return $this->respondHttp200();

      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }

    public function multiselectConditions(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $conditions = Condition::select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($conditions)
            ]);
        }
        else
        {
            $conditions = Condition::selectRaw("
                sau_ph_conditions.id as id,
                sau_ph_conditions.description as description
            ")->pluck('id', 'description');
        
            return $this->multiSelectFormat($conditions);
        }
    }
}
