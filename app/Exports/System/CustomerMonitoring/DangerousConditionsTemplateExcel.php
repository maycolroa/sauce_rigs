<?php

namespace App\Exports\System\CustomerMonitoring;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use App\Models\General\Company;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerousConditionsTemplateExcel implements FromQuery, WithMapping, WithStrictNullComparison, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
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
            
      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->started_at,
        $data->ended_at,
        $data->report_mes,
        $data->report_manio,
        $data->insp_mes,
        $data->insp_anio

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
        'Reportes creados este año',
        'Inspecciones realizadas este mes',
        'Inspecciones realizadas este año'
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
      return 'Condiciones Peligrosas';
    }
}


