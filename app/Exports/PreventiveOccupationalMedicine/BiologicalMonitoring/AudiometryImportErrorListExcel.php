<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class AudiometryImportErrorListExcel implements FromView, WithEvents, WithTitle
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
            'font' => [
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
        return 'Lista de Errores';
    }

    public function view(): View
    {
        return view('mail.preventiveoccupationalmedicine.biologicalmonitoring.audiometry-import-errors-list', [
            'errors' => $this->errors
        ]);
    }
}
