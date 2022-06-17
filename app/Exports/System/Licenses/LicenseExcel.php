<?php

namespace App\Exports\System\Licenses;

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
use App\Traits\UtilsTrait;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class LicenseExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    public function query()
    {
      $licenses = License::system()
      ->selectRaw(
          'sau_licenses.*,
              GROUP_CONCAT(" ", sau_modules.display_name ORDER BY sau_modules.display_name) AS modules,
              sau_companies.name AS company'
      )
      ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
      ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
      ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
      ->where('sau_modules.main', 'SI')
      ->groupBy('sau_licenses.id');

      if (isset($this->filters["modules"]) && COUNT($this->filters["modules"]) > 0)
          $licenses->inModules($this->filters["modules"], $this->filters['filtersType']['modules']);

      $dates = [];

      if (isset($this->filters["dates"]) && COUNT($this->filters["dates"]) > 0)  
        $licenses->betweenDate($this->filters["dates"]);
          
      return $licenses;
    }

    public function map($data): array
    {
      $values = [
        $data->company,
        $data->started_at,
        $data->ended_at,
        $data->created_at,
        $data->modules
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Fecha inicio',
        'Fecha fin',
        'Fecha de creación',
        'Módulos'
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


