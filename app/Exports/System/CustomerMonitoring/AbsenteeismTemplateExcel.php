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
use App\Models\PreventiveOccupationalMedicine\Absenteeism\MonitorReportView;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class AbsenteeismTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
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

      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->started_at,
        $data->ended_at,
        $data->report_mes,
        $data->report_anio,
        $data->file_mes,
        $data->file_anio
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Fecha inicio licencia',
        'Fecha fin licencia',
        'Reportes vistos este mes',
        'Reportes vistos este año',
        'Archivos cargados este mes',
        'Archivos cargados este año',
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
      return 'Ausentismo';
    }
}


