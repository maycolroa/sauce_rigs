<?php

namespace App\Exports\System\CustomerMonitoring;

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
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\System\LogMails\LogMail;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class LegalMatrixTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
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


      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->started_at,
        $data->ended_at,
        $data->cal_mes,
        $data->cal_anio,
        $data->email_mes,
        $data->email_anio
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Fecha inicio licencia',
        'Fecha fin licencia',
        'Artículos calificados este mes',
        'Artículos calificados este año',
        'Emails vistos este mes',
        'Emails vistos este año',
      ];

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:R1',
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
      return 'Matríz Legal';
    }
}


