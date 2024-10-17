<?php 

namespace App\Exports\LegalAspects\Contracts\Contracts;

use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\General\Departament;
use App\Models\General\Municipality;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\UtilsTrait;

class DepartamentMunicipalityTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $keywords;

    public function __construct($company_id)
    {
    }

    /**
    * @var eps $eps
    */
    public function map($record): array
    {
        return [
            $record->codeD,
            $record->name_departament,
            $record->codeM,
            $record->name_muinicipality
        ];
    }

    public function headings(): array
    {
        return [
            'CódigoD',
            'Departamento',
            'CódigoM',
            'Municipio',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Departamentos y municipios';
    }

    public function query()
    {
        $records = Departament::select(
          'sau_departaments.id AS codeD',
          'sau_departaments.name AS name_departament',
          'sau_municipalities.id AS codeM',
          'sau_municipalities.name AS name_muinicipality'
        )
        ->join('sau_municipalities', 'sau_municipalities.departament_id', 'sau_departaments.id');

        return $records;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:C1',
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
}