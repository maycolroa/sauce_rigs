<?php 

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\UtilsTrait;

class ContractsImportLeyendTemplate implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $data;

    public function __construct($data)
    {
      $this->data = $data;
    }

    public function collection()
    {
      return $this->data;
    }

    public function map($data): array
    {
      $result = [];

      foreach ($data as $key => $value)
      {
        array_push($result, $value);
      }
      
      return $result;
    }


    public function headings(): array
    {
        return [
            'Leyenda'
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Leyenda';
    }

    public static function afterSheet(AfterSheet $event)
    {
      $red = "d9534f";
      $blue = "5Bc0de";
      $yellow = "f4d75e";      
      $white = "FFFFFF";

      $event->sheet->styleCells(
        'A1',
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

      $event->sheet->styleCells(
        'A3',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $yellow
                ],
            ]
          ]
      );

      $event->sheet->styleCells(
        'A4',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $red
                ],
            ]
          ]
      );

      $event->sheet->styleCells(
        'A5',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $blue
                ],
            ]
          ]
      );
    }
}