<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class AudiometryImportErrorsExcel implements FromView, WithEvents
{
  use RegistersEventListeners;

    protected $errors;

    public function __construct($errors)
    {
      $this->errors = $errors;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:K1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
              'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
              ],
            ],
            'font' => [
              'bold' => true,
            ]
          ]
      );
    }

    public function view(): View
    {
        return view('mail.preventiveoccupationalmedicine.biologicalmonitoring.audiometryimporterrors', [
            'errors' => $this->errors
        ]);
    }
}
