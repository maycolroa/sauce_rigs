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
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class CompaniesTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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

      $companies = Company::selectRaw("
        sau_companies.id,
        sau_companies.name as name,
        sau_companies.active as active
      ")
      ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
      ->withoutGlobalScopes()
      ->whereRaw('? BETWEEN sau_licenses.started_at AND sau_licenses.ended_at', [date('Y-m-d')])
      ->where('sau_companies.company_group_id', DB::raw($this->group->id))
      ->where('sau_companies.active', 'SI')
      ->groupby('sau_companies.id');

      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->active
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Nombre',
        '¿Activa?'
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
      return 'Compañias';
    }
}


