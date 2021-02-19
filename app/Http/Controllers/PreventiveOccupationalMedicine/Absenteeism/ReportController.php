<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Report;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\MonitorReportView;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\ReportRequest;
use Config;
use DB;

class ReportController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:absen_reports_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:absen_reports_r, {$this->team}");
        $this->middleware("permission:absen_reports_u|absen_reports_admin_user, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:absen_reports_d, {$this->team}", ['only' => 'destroy']);
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
        if($this->user->can('absen_reports_view_all', $this->team)){
            $reports = Report::select('*');
        }
        else{
            $reports = Report::select(
                'sau_absen_reports.*'
            )
            ->join('sau_absen_report_user', 'sau_absen_report_user.report_id', 'sau_absen_reports.id')
            ->where('sau_absen_report_user.user_id', $this->user->id);
        }
        
        return Vuetable::of($reports)
                    ->make();
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

            $user_id = [];

            foreach ($report->users as $key => $value)
            {
              array_push($user_id, $value->multiselect());
            }
            
            $report->user_id = $user_id;
            $report->multiselect_user_id = $user_id;
            $report->url= $report->getTableauCode()->generateReportURL();
            
            return $this->respondHttp200([
                'data' => $report,
            ]);
            
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\ReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReportRequest $request)
    {
        DB::beginTransaction();

        try
        {

            $report = new Report($request->all());
            $report->company_id = $this->company;
            $report->type=1;
            $report->es_bsc=0;

            if(!$report->save()){
                return $this->respondHttp500();
            }

            if ($request->get('user_id') && COUNT($request->get('user_id')) > 0)
            {
                $report->users()->sync($this->getDataFromMultiselect($request->get('user_id')));
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //return $e->getMessage();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se creo el informe'
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\ReportRequest  $request
     * @param  Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(ReportRequest $request, Report $report)
    {
        DB::beginTransaction();

        try{

            $report->fill($request->all());
            
            if(!$report->update()){
                return $this->respondHttp500();
            }

            if ($request->get('user_id') && COUNT($request->get('user_id')) > 0)
            {
                $report->users()->sync($this->getDataFromMultiselect($request->get('user_id')));
            }

            DB::commit();

        }
        catch(\Exception $e) {
            DB::rollback();
            //return $e->getMessage();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el informe'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        if (!$report->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino el informe'
        ]);
    }

    public function monitorViews($id)
    {
        $view = new MonitorReportView;
        $view->report_id = $id;
        $view->user_id = $this->user->id;
        $view->company_id = $this->company;
        $view->save();
    }
}
