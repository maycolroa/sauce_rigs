<?php

namespace App\Exports\System\Licenses;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ReportGroupExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $data;
    protected $headers;

    public function __construct($data, $headers)
    {
      $this->headers = $headers;
      $this->data = collect($data);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function map($data): array
    {
      $check = [
        $data['group'],
        $data['new_old'] ? $data['new_old'] : '0',
        $data['renew_old'] ? $data['renew_old'] : '0',
        $data['total_old'] ? $data['total_old'] : '0',
        $data['new'] ? $data['new'] : '0',
        $data['renew'] ? $data['renew'] : '0',
        $data['total'] ? $data['total'] : '0',
        $data['retention'] ? $data['retention'].'%' : '0%',
        $data['crecimiento'] ? $data['crecimiento'].'%': '0%',
      ];

      return $check;

    }

    public function headings(): array
    {
      return $this->headers;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:M1',
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
        return 'Grupo de Compañia';
    }
}

