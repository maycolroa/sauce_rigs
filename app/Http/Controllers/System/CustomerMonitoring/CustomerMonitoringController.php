<?php

namespace App\Http\Controllers\System\CustomerMonitoring;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\MonitorReportView;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use Carbon\Carbon;
use Validator;
use DB;

class CustomerMonitoringController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:licenses_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:licenses_r, {$this->team}");
        $this->middleware("permission:licenses_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:licenses_d, {$this->team}", ['only' => 'destroy']);*/
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
    public function dataReinstatements(Request $request)
    {
        $now = Carbon::now();

        $reports = Check::select(
            "sau_reinc_checks.company_id AS company_id",
            DB::raw('COUNT(DISTINCT sau_reinc_checks.employee_id) AS total'),
            DB::raw("SUM(CASE WHEN YEAR(sau_reinc_checks.created_at) = {$now->year} AND MONTH(sau_reinc_checks.created_at) = {$now->month} THEN 1 ELSE 0 END) AS total_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_reinc_checks.created_at) = {$now->year} THEN 1 ELSE 0 END) AS total_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_reinc_checks.company_id');  
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.total, 0) AS total,
                IFNULL(t.total_mes, 0) AS total_mes,
                IFNULL(t.total_anio, 0) AS total_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$reports->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($reports->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 21)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataDangerousConditions(Request $request)
    {
        $now = Carbon::now();

        $reports = Report::select(
            "sau_ph_reports.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ph_reports.created_at) = {$now->year} AND MONTH(sau_ph_reports.created_at) = {$now->month} THEN 1 ELSE 0 END) AS report_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ph_reports.created_at) = {$now->year} THEN 1 ELSE 0 END) AS report_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_ph_reports.company_id');
        
        $inspections = InspectionItemsQualificationAreaLocation::select(
            "sau_ph_inspections.company_id AS company_id",
            DB::raw("COUNT(DISTINCT CASE WHEN YEAR(sau_ph_inspection_items_qualification_area_location.qualification_date) = {$now->year} AND MONTH(sau_ph_inspection_items_qualification_area_location.qualification_date) = {$now->month} THEN sau_ph_inspection_items_qualification_area_location.qualification_date END) AS insp_mes"),
            DB::raw("COUNT(DISTINCT CASE WHEN YEAR(sau_ph_inspection_items_qualification_area_location.qualification_date) = {$now->year} THEN sau_ph_inspection_items_qualification_area_location.qualification_date END) AS insp_anio")
        )
        ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
        ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
        ->join('sau_ph_inspections', 'sau_ph_inspections.id', 'sau_ph_inspection_sections.inspection_id')        
        ->withoutGlobalScopes()
        ->groupBy('sau_ph_inspections.company_id');
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.report_mes, 0) AS report_mes,
                IFNULL(t.report_anio, 0) AS report_anio,
                IFNULL(t2.insp_mes, 0) AS insp_mes,
                IFNULL(t2.insp_anio, 0) AS insp_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$reports->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($reports->getQuery())
            ->leftJoin(DB::raw("({$inspections->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($inspections->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 26)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataAutomaticsSend(Request $request)
    {
        
    }

    public function dataAbsenteeism(Request $request)
    {
        $now = Carbon::now();

        $reports = MonitorReportView::select(
            "sau_absen_monitor_report_views.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_absen_monitor_report_views.created_at) = {$now->year} AND MONTH(sau_absen_monitor_report_views.created_at) = {$now->month} THEN 1 ELSE 0 END) AS report_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_absen_monitor_report_views.created_at) = {$now->year} THEN 1 ELSE 0 END) AS report_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_absen_monitor_report_views.company_id');
        
        $files = FileUpload::select(
            "sau_absen_file_upload.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_absen_file_upload.created_at) = {$now->year} AND MONTH(sau_absen_file_upload.created_at) = {$now->month} THEN 1 ELSE 0 END) AS file_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_absen_file_upload.created_at) = {$now->year} THEN 1 ELSE 0 END) AS file_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_absen_file_upload.company_id');
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.report_mes, 0) AS report_mes,
                IFNULL(t.report_anio, 0) AS report_anio,
                IFNULL(t2.file_mes, 0) AS file_mes,
                IFNULL(t2.file_anio, 0) AS file_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$reports->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($reports->getQuery())
            ->leftJoin(DB::raw("({$files->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($files->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 24)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
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
            $company = Company::findOrFail($id);
            $company->users = [];
            $company->delete = [];

            return $this->respondHttp200([
                'data' => $company,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CompanyRequest  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        DB::beginTransaction();

        try
        {
            $company->fill($request->all());
            
            if(!$company->update())
                return $this->respondHttp500();

            $team = Team::updateOrCreate(
                [
                    'name' => $company->id,
                ], 
                [
                    'id' => $company->id,
                    'name' => $company->id,
                    'display_name' => $company->name,
                    'description' => "Equipo ".$company->name
                ]
            );

            if ($request->has('users') && COUNT($request->users) > 0)
            {
                foreach ($request->users as $key => $value)
                {
                    $user = User::find($value['user_id']);

                    if ($user)
                    {
                        $user->companies()->attach($company->id);

                        $roles = $this->getValuesForMultiselect($value['role_id']);
                        $roles = Role::whereIn('id', $roles)->get();

                        if (COUNT($roles) > 0)
                        {
                            $team = Team::where('name', $company->id)->first();
                            $user->attachRoles($roles, $team);
                        }
                    }
                }
            }
                
            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la compañia'
        ]);
    }
}
