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
      
      $companies = Company::selectRaw('
              sau_companies.id AS id,
              sau_companies.name AS name,
              MAX(sau_licenses.started_at) AS started_at,
              MAX(sau_licenses.ended_at) AS ended_at,
              IFNULL(t.cal_mes, 0) AS cal_mes,
              IFNULL(t.cal_anio, 0) AS cal_anio'
          )
          ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
          ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
          ->leftJoin(DB::raw("({$articles->toSql()}) as t"), function ($join) {
              $join->on("t.company_id", "sau_companies.id");
          })
          ->mergeBindings($articles->getQuery())
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
        /*$data->file_mes,
        $data->file_anio*/
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
        /*'Archivos cargados este mes',
        'Archivos cargados este año',*/
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


