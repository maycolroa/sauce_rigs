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
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class LicensesTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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

      $companies = Company::where('sau_companies.company_group_id', DB::raw($this->group->id))
      ->withoutGlobalScopes()
      ->where('sau_companies.active', 'SI')
      ->pluck('id')
      ->toArray();

      $licenses = DB::table('sau_licenses')->selectRaw("
        sau_modules.display_name as name,
        count(license_id) as active
      ")
      ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
      ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
      ->whereIn('sau_licenses.company_id', $companies)
      ->whereRaw('? BETWEEN sau_licenses.started_at AND sau_licenses.ended_at', [date('Y-m-d')])
      ->where('sau_modules.main', 'SI')
      ->groupby('sau_license_module.module_id')
      ->orderBy('sau_modules.id');

      return $licenses;
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
        'Modulo',
        'Activas'
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
      return 'Licencias';
    }
}


