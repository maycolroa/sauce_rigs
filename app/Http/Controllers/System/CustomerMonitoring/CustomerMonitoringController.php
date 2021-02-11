<?php

namespace App\Http\Controllers\System\CustomerMonitoring;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
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
}
