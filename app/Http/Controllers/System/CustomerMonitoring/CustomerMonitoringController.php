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
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\LegalAspects\Contracts\FileUpload AS FileContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\System\CustomerMonitoring\NotificationScheduled;
use App\Models\System\CustomerMonitoring\Notification;
use App\Models\System\LogMails\LogMail;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\EppExit;
use App\Models\IndustrialSecure\Epp\EppIncome;
use App\Models\IndustrialSecure\Epp\EppTransfer;
use App\Models\IndustrialSecure\Epp\EppReception;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
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

    public function dataDangerMatrix(Request $request)
    {
        $now = Carbon::now();

        $dangerMatrix = DangerMatrix::select(
            "sau_dangers_matrix.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_dangers_matrix.updated_at) = {$now->year} AND MONTH(sau_dangers_matrix.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS total_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_dangers_matrix.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS total_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_dangers_matrix.company_id');  
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.total_mes, 0) AS total_mes,
                IFNULL(t.total_anio, 0) AS total_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$dangerMatrix->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($dangerMatrix->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 14)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataRiskMatrix(Request $request)
    {
        $now = Carbon::now();

        $riskMatrix = RiskMatrix::select(
            "sau_rm_risks_matrix.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_rm_risks_matrix.updated_at) = {$now->year} AND MONTH(sau_rm_risks_matrix.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS total_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_rm_risks_matrix.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS total_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_rm_risks_matrix.company_id');  
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.total_mes, 0) AS total_mes,
                IFNULL(t.total_anio, 0) AS total_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$riskMatrix->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($riskMatrix->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 31)
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

    public function dataContract(Request $request)
    {
        $now = Carbon::now();

        $contract = ContractLesseeInformation::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_information_contract_lessee.created_at) = {$now->year} AND MONTH(sau_ct_information_contract_lessee.created_at) = {$now->month} THEN 1 ELSE 0 END) AS contract_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_information_contract_lessee.created_at) = {$now->year} THEN 1 ELSE 0 END) AS contract_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        $evaluations = EvaluationContract::select(
            "sau_ct_evaluation_contract.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_contract.updated_at) = {$now->year} AND MONTH(sau_ct_evaluation_contract.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS eva_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_contract.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS eva_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_ct_evaluation_contract.company_id');

        $qualifications = ItemQualificationContractDetail::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} AND MONTH(sau_ct_item_qualification_contract.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS cal_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS cal_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_item_qualification_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        $list_files = FileContract::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->year} AND MONTH(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS file_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS file_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_file_upload_contract', 'sau_ct_file_upload_contract.file_upload_id', 'sau_ct_file_upload_contracts_leesse.id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        
        $eva_files = EvaluationFile::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_item_files.updated_at) = {$now->year} AND MONTH(sau_ct_evaluation_item_files.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS file_e_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_item_files.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS file_e_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_evaluation_contract', 'sau_ct_evaluation_contract.id', 'sau_ct_evaluation_item_files.evaluation_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.contract_mes, 0) AS contract_mes,
                IFNULL(t.contract_anio, 0) AS contract_anio,
                IFNULL(t2.eva_mes, 0) AS eva_mes,
                IFNULL(t2.eva_anio, 0) AS eva_anio,
                IFNULL(t3.cal_mes, 0) AS cal_mes,
                IFNULL(t3.cal_anio, 0) AS cal_anio,
                IFNULL(t4.file_mes, 0) + IFNULL(t5.file_e_mes, 0) AS file_mes,
                IFNULL(t4.file_anio, 0) + IFNULL(t5.file_e_anio, 0) AS file_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$contract->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($contract->getQuery())
            ->leftJoin(DB::raw("({$evaluations->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($evaluations->getQuery())
            ->leftJoin(DB::raw("({$qualifications->toSql()}) as t3"), function ($join) {
                $join->on("t3.company_id", "sau_companies.id");
            })
            ->mergeBindings($qualifications->getQuery())
            ->leftJoin(DB::raw("({$list_files->toSql()}) as t4"), function ($join) {
                $join->on("t4.company_id", "sau_companies.id");
            })
            ->mergeBindings($list_files->getQuery())
            ->leftJoin(DB::raw("({$eva_files->toSql()}) as t5"), function ($join) {
                $join->on("t5.company_id", "sau_companies.id");
            })
            ->mergeBindings($eva_files->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 16)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataLegalMatrix(Request $request)
    {
        $now = Carbon::now();

        $articles = ArticleFulfillment::select(
            "sau_lm_articles_fulfillment.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_lm_articles_fulfillment.updated_at) = {$now->year} AND MONTH(sau_lm_articles_fulfillment.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS cal_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_lm_articles_fulfillment.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS cal_anio")
        )
        ->withoutGlobalScopes()
        ->where('sau_lm_articles_fulfillment.created_at', '<>', 'sau_lm_articles_fulfillment.updated_at')
        ->groupBy('sau_lm_articles_fulfillment.company_id');  

        $emails = LogMail::select(
            "sau_log_mails.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sent_emails.updated_at) = {$now->year} AND MONTH(sent_emails.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS email_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sent_emails.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS email_anio")
        )
        ->withoutGlobalScopes()
        ->join('sent_emails', 'sent_emails.message_id', 'sau_log_mails.message_id')
        ->where('sau_log_mails.module_id', 17)
        ->where('sent_emails.opens', '>', 0)
        ->groupBy('sau_log_mails.company_id'); 

        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(SUM(t.cal_mes), 0) AS cal_mes,
                IFNULL(SUM(t.cal_anio), 0) AS cal_anio,
                IFNULL(SUM(t2.email_mes), 0) AS email_mes,
                IFNULL(SUM(t2.email_anio), 0) AS email_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$articles->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($articles->getQuery())
            ->leftJoin(DB::raw("({$emails->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($emails->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 17)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataEpp(Request $request)
    {
        $now = Carbon::now();

        $elements = Element::select(
            "sau_epp_elements.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_elements.created_at) = {$now->year} AND MONTH(sau_epp_elements.created_at) = {$now->month} THEN 1 ELSE 0 END) AS element_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_elements.created_at) = {$now->year} THEN 1 ELSE 0 END) AS element_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_elements.company_id');

        $incomen = EppIncome::select(
            "sau_epp_incomen.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_incomen.updated_at) = {$now->year} AND MONTH(sau_epp_incomen.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS incomen_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_incomen.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS incomen_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_incomen.company_id');

        $exits = EppExit::select(
            "sau_epp_exits.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_exits.updated_at) = {$now->year} AND MONTH(sau_epp_exits.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS exit_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_exits.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS exit_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_exits.company_id');

        $transfers = EppTransfer::select(
            "sau_epp_transfers.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transfers.updated_at) = {$now->year} AND MONTH(sau_epp_transfers.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS transfer_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transfers.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS transfer_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_transfers.company_id');

        
        $reception = EppReception::select(
            "sau_epp_receptions.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_receptions.updated_at) = {$now->year} AND MONTH(sau_epp_receptions.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS reception_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_receptions.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS reception_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_receptions.company_id');

        $delivery = ElementTransactionEmployee::select(
            "sau_epp_transactions_employees.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transactions_employees.updated_at) = {$now->year} AND MONTH(sau_epp_transactions_employees.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS delivery_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transactions_employees.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS delivery_anio")
        )
        ->where('sau_epp_transactions_employees.type', DB::raw("'Entrega'"))
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_transactions_employees.company_id');

        $return = ElementTransactionEmployee::select(
            "sau_epp_transactions_employees.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transactions_employees.updated_at) = {$now->year} AND MONTH(sau_epp_transactions_employees.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS return_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_epp_transactions_employees.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS return_anio")
        )
        ->where('sau_epp_transactions_employees.type', DB::raw("'Devolucion'"))
        ->withoutGlobalScopes()
        ->groupBy('sau_epp_transactions_employees.company_id');
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.element_mes, 0) AS element_mes,
                IFNULL(t.element_anio, 0) AS element_anio,
                IFNULL(t2.incomen_mes, 0) AS incomen_mes,
                IFNULL(t2.incomen_anio, 0) AS incomen_anio,
                IFNULL(t3.exit_mes, 0) AS exit_mes,
                IFNULL(t3.exit_anio, 0) AS exit_anio,
                IFNULL(t4.transfer_mes, 0) AS transfer_mes,
                IFNULL(t4.transfer_anio, 0) AS transfer_anio,
                IFNULL(t5.reception_mes, 0)  AS reception_mes,
                IFNULL(t5.reception_anio, 0)  AS reception_anio,
                IFNULL(t6.delivery_mes, 0) AS delivery_mes,
                IFNULL(t6.delivery_anio, 0) AS delivery_anio,
                IFNULL(t7.return_mes, 0) AS return_mes,
                IFNULL(t7.return_anio, 0) AS return_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$elements->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($elements->getQuery())
            ->leftJoin(DB::raw("({$incomen->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($incomen->getQuery())
            ->leftJoin(DB::raw("({$exits->toSql()}) as t3"), function ($join) {
                $join->on("t3.company_id", "sau_companies.id");
            })
            ->mergeBindings($exits->getQuery())
            ->leftJoin(DB::raw("({$transfers->toSql()}) as t4"), function ($join) {
                $join->on("t4.company_id", "sau_companies.id");
            })
            ->mergeBindings($transfers->getQuery())
            ->leftJoin(DB::raw("({$reception->toSql()}) as t5"), function ($join) {
                $join->on("t5.company_id", "sau_companies.id");
            })
            ->mergeBindings($reception->getQuery())
            ->leftJoin(DB::raw("({$delivery->toSql()}) as t6"), function ($join) {
                $join->on("t6.company_id", "sau_companies.id");
            })
            ->mergeBindings($delivery->getQuery())
            ->leftJoin(DB::raw("({$return->toSql()}) as t7"), function ($join) {
                $join->on("t7.company_id", "sau_companies.id");
            })
            ->mergeBindings($return->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 32)
            ->groupBy('sau_companies.id');

        return Vuetable::of($companies)
                    ->make();
    }

    public function dataAutomaticsSend(Request $request)
    {
        $sends = Notification::select('*');

        return Vuetable::of($sends)
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
            $notification = Notification::findOrFail($id);

            $user_id = [];

            foreach ($notification->users as $key => $value)
            {                
                array_push($user_id, $value->multiselect());
            }

            $notification->multiselect_users = $user_id;
            $notification->user_id = $user_id;
            
            $days = [];

            foreach ($notification->days as $key => $value)
            {                
                array_push($days, $value->multiselect());
            }

            $notification->multiselect_days = $days;
            $notification->day_id = $days;  

            return $this->respondHttp200([
                'data' => $notification,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
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
    public function update(Request $request, Notification $notification)
    {
        DB::beginTransaction();

        try
        {
            if ($request->has('user_id') && COUNT($request->user_id) > 0)
            {
                $user_id = $this->getDataFromMultiselect($request->get('user_id'));
                $notification->users()->sync($user_id);
            }

            if ($request->has('day_id') && COUNT($request->day_id) > 0)
            {
                $days = $this->getDataFromMultiselect($request->get('day_id'));
                $days_conf = $notification->days->pluck('day')->toArray();

                foreach ($days as $day)
                {
                    if (!in_array($day, $days_conf))
                    {
                        $record = new NotificationScheduled;
                        $record->day = $day;
                        $record->notification_id = $notification->id;
                        $record->save();
                    }
                }
                
                NotificationScheduled::
                    where('notification_id', $notification->id)->whereNotIn('day', $days)->delete();
            }
                
            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la notificación'
        ]);
    }
}
