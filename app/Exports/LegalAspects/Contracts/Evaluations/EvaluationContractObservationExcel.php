<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationContractObservationExcel implements FromView, WithEvents, WithTitle, ShouldAutoSize
{
  use RegistersEventListeners;

    protected $observation;

    public function __construct($observation)
    {
      $this->observation = $observation;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:J1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
              'bold' => true,
            ]
          ]
      );

      $event->sheet->styleCells(
        'A2:J2',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ]
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Observación General';
    }

    public function view(): View
    {
        return view('mail.LegalAspects.contract.evaluations.generalObservation', [
            'observation' => $this->observation
        ]);
    }
}
