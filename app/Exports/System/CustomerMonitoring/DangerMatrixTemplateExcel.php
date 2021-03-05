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
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
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
        'Matrices creadas o modificadas este mes',
        'Matrices creadas o modificadas este año'
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
      return 'Matríz de peligros';
    }
}


