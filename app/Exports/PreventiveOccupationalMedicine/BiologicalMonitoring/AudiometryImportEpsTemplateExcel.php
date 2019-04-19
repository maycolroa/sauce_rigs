<?php 

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Models\Administrative\Employees\EmployeeEPS;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

class AudiometryImportEpsTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents
{
    use RegistersEventListeners;

    /**
    * @var eps $eps
    */
    public function map($eps): array
    {
        return [
            $eps->code,
            $eps->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'EPS';
    }

    public function query()
    {
        return EmployeeEPS::query();
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:B1',
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