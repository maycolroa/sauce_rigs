<?php

namespace App\Exports\System\CompanyGroup;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use App\Models\General\Company;
use App\Models\System\Licenses\License;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\LegalAspects\Contracts\FileUpload AS FileContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\MonitorReportView;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Models\System\LogMails\LogMail;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class LicensesUseTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $group;

    public function __construct($group)
    {
      $this->group = $group;
    }

    public function query()
    {
      $now = Carbon::now();

      $companies_group = Company::where('sau_companies.company_group_id', DB::raw($this->group->id))
      ->withoutGlobalScopes()
      ->where('sau_companies.active', 'SI')
      ->pluck('id')
      ->toArray();

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

      $reports = Check::select(
        "sau_reinc_checks.company_id AS company_id",
        DB::raw('COUNT(DISTINCT sau_reinc_checks.employee_id) AS total_reinc'),
        DB::raw("SUM(CASE WHEN YEAR(sau_reinc_checks.created_at) = {$now->year} AND MONTH(sau_reinc_checks.created_at) = {$now->month} THEN 1 ELSE 0 END) AS total_mes_reinc"),
        DB::raw("SUM(CASE WHEN YEAR(sau_reinc_checks.created_at) = {$now->year} THEN 1 ELSE 0 END) AS total_anio_reinc")
    )
    ->withoutGlobalScopes()
    ->groupBy('sau_reinc_checks.company_id');

      $reports_reinc = Report::select(
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

      $dangerMatrix = DangerMatrix::select(
        "sau_dangers_matrix.company_id AS company_id",
        DB::raw("SUM(CASE WHEN YEAR(sau_dangers_matrix.updated_at) = {$now->year} AND MONTH(sau_dangers_matrix.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS total_mes"),
        DB::raw("SUM(CASE WHEN YEAR(sau_dangers_matrix.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS total_anio")
      )
      ->withoutGlobalScopes()
      ->groupBy('sau_dangers_matrix.company_id');

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
          DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} AND MONTH(sau_ct_item_qualification_contract.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS cal_ct_mes"),
          DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS cal_ct_anio")
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

      $reports_abs = MonitorReportView::select(
        "sau_absen_monitor_report_views.company_id AS company_id",
        DB::raw("SUM(CASE WHEN YEAR(sau_absen_monitor_report_views.created_at) = {$now->year} AND MONTH(sau_absen_monitor_report_views.created_at) = {$now->month} THEN 1 ELSE 0 END) AS report_abs_mes"),
        DB::raw("SUM(CASE WHEN YEAR(sau_absen_monitor_report_views.created_at) = {$now->year} THEN 1 ELSE 0 END) AS report_abs_anio")
      )
      ->withoutGlobalScopes()
      ->groupBy('sau_absen_monitor_report_views.company_id');
      
      $files = FileUpload::select(
          "sau_absen_file_upload.company_id AS company_id",
          DB::raw("SUM(CASE WHEN YEAR(sau_absen_file_upload.created_at) = {$now->year} AND MONTH(sau_absen_file_upload.created_at) = {$now->month} THEN 1 ELSE 0 END) AS file_abs_mes"),
          DB::raw("SUM(CASE WHEN YEAR(sau_absen_file_upload.created_at) = {$now->year} THEN 1 ELSE 0 END) AS file_abs_anio")
      )
      ->withoutGlobalScopes()
      ->groupBy('sau_absen_file_upload.company_id');

      $companies = DB::table('sau_companies')->selectRaw('
              sau_companies.id AS id,
              sau_companies.name AS name,
              IFNULL(SUM(t.cal_mes), 0) AS cal_mes,
              IFNULL(SUM(t.cal_anio), 0) AS cal_anio,
              IFNULL(SUM(t2.email_mes), 0) AS email_mes,
              IFNULL(SUM(t2.email_anio), 0) AS email_anio,
              IFNULL(t3.total_mes_reinc, 0) AS total_mes_reinc,
              IFNULL(t3.total_anio_reinc, 0) AS total_anio_reinc,              
              IFNULL(t4.report_mes, 0) AS report_insp_mes,
              IFNULL(t4.report_anio, 0) AS report_insp_anio,
              IFNULL(t5.insp_mes, 0) AS insp_mes,
              IFNULL(t5.insp_anio, 0) AS insp_anio,              
              IFNULL(t6.total_mes, 0) AS total_dm_mes,
              IFNULL(t6.total_anio, 0) AS total_dm_anio,
              IFNULL(t7.contract_mes, 0) AS contract_mes,
              IFNULL(t7.contract_anio, 0) AS contract_anio,
              IFNULL(t8.eva_mes, 0) AS eva_mes,
              IFNULL(t8.eva_anio, 0) AS eva_anio,
              IFNULL(t9.cal_ct_mes, 0) AS cal_ct_mes,
              IFNULL(t9.cal_ct_anio, 0) AS cal_ct_anio,
              IFNULL(t10.file_mes, 0) + IFNULL(t11.file_e_mes, 0) AS file_mes,
              IFNULL(t10.file_anio, 0) + IFNULL(t11.file_e_anio, 0) AS file_anio,
              IFNULL(t12.report_abs_mes, 0) AS report_abs_mes,
              IFNULL(t12.report_abs_anio, 0) AS report_abs_anio,
              IFNULL(t13.file_abs_mes, 0) AS file_abs_mes,
              IFNULL(t13.file_abs_anio, 0) AS file_abs_anio'
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
          ->leftJoin(DB::raw("({$reports->toSql()}) as t3"), function ($join) {
            $join->on("t3.company_id", "sau_companies.id");
          })
          ->mergeBindings($reports->getQuery())
          ->leftJoin(DB::raw("({$reports_reinc->toSql()}) as t4"), function ($join) {
            $join->on("t4.company_id", "sau_companies.id");
          })
          ->mergeBindings($reports_reinc->getQuery())
          ->leftJoin(DB::raw("({$inspections->toSql()}) as t5"), function ($join) {
              $join->on("t5.company_id", "sau_companies.id");
          })
          ->mergeBindings($inspections->getQuery())
          ->leftJoin(DB::raw("({$dangerMatrix->toSql()}) as t6"), function ($join) {
            $join->on("t6.company_id", "sau_companies.id");
          })
          ->mergeBindings($dangerMatrix->getQuery())
          ->leftJoin(DB::raw("({$contract->toSql()}) as t7"), function ($join) {
            $join->on("t7.company_id", "sau_companies.id");
          })
          ->mergeBindings($contract->getQuery())
          ->leftJoin(DB::raw("({$evaluations->toSql()}) as t8"), function ($join) {
              $join->on("t8.company_id", "sau_companies.id");
          })
          ->mergeBindings($evaluations->getQuery())
          ->leftJoin(DB::raw("({$qualifications->toSql()}) as t9"), function ($join) {
              $join->on("t9.company_id", "sau_companies.id");
          })
          ->mergeBindings($qualifications->getQuery())
          ->leftJoin(DB::raw("({$list_files->toSql()}) as t10"), function ($join) {
              $join->on("t10.company_id", "sau_companies.id");
          })
          ->mergeBindings($list_files->getQuery())
          ->leftJoin(DB::raw("({$eva_files->toSql()}) as t11"), function ($join) {
              $join->on("t11.company_id", "sau_companies.id");
          })
          ->mergeBindings($eva_files->getQuery())
          ->leftJoin(DB::raw("({$reports_abs->toSql()}) as t12"), function ($join) {
            $join->on("t12.company_id", "sau_companies.id");
          })
          ->mergeBindings($reports_abs->getQuery())
          ->leftJoin(DB::raw("({$files->toSql()}) as t13"), function ($join) {
              $join->on("t13.company_id", "sau_companies.id");
          })
          ->mergeBindings($files->getQuery())
          ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
          ->whereIn('sau_licenses.company_id', $companies_group)
          ->groupBy('sau_companies.id')
          ->orderBy('sau_companies.name');

      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->cal_mes,
        $data->cal_anio,
        $data->email_mes,
        $data->email_anio,
        $data->total_mes_reinc,
        $data->total_anio_reinc,
        $data->insp_mes,
        $data->insp_anio,
        $data->report_insp_mes,
        $data->report_insp_anio,
        $data->total_dm_mes,
        $data->total_dm_anio,
        $data->contract_mes,
        $data->contract_anio,
        $data->eva_mes,
        $data->eva_anio,
        $data->cal_ct_mes,
        $data->cal_ct_anio,
        $data->file_mes,
        $data->file_anio,
        $data->report_abs_mes,
        $data->report_abs_anio,
        $data->file_abs_mes,
        $data->file_abs_anio
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Artículos calificados este mes (Matriz Legal)',
        'Artículos calificados este año (Matriz Legal)',
        'Emails vistos este mes (Matriz Legal)',
        'Emails vistos este año (Matriz Legal)',
        'Reportes creados este mes (Reincorporaciones)',
        'Reportes creados este año (Reincorporaciones)',
        'Inspecciones planeadas realizadas este mes',
        'Inspecciones planeadas realizadas este año',
        'Inspecciones no planeadas realizadas este mes',
        'Inspecciones no planeadas realizadas este año',
        'Matrices de peligros creadas o modificadas este mes',
        'Matrices de peligros creadas o modificadas este año',
        'Contratistas creados este mes',
        'Contratistas creados este año',
        'Evaluaciones a contratistas creadas o modificadas este mes',
        'Evaluaciones a contratistas creadas o modificadas este año',
        'Items de lista de chequeo califiados este mes',
        'Items de lista de chequeo califiados este año',
        'Archivos cargados este mes (Contratistas)',
        'Archivos cargados este año (Contratistas)',
        'Reportes vistos este mes (Ausentismo)',
        'Reportes vistos este año (Ausentismo)',
        'Archivos cargados este mes (Ausentismo)',
        'Archivos cargados este año (Ausentismo)',
      ];

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AZ1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
               'name' => 'Arial', 
               'bold' => true,
            ]
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Monitoreo';
    }
}


