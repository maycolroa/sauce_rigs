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
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ReinstatementsTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
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
            
      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->started_at,
        $data->ended_at,
        $data->total_mes,
        $data->total_anio
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Fecha inicio licencia',
        'Fecha fin licencia',
        'Reportes creados este mes',
        'Reportes creados este año'
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
      return 'Reincorporaciones';
    }
}


