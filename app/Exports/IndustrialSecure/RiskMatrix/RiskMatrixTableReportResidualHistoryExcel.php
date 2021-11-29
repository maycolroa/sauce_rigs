<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use \Maatwebsite\Excel\Sheet;
use App\Facades\Administrative\KeywordManager;
use App\Models\IndustrialSecure\RiskMatrix\ReportHistory;
use App\Traits\RiskMatrixTrait;
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class RiskMatrixTableReportResidualHistoryExcel implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;
    use RiskMatrixTrait;
    use LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $data;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);

      $data = [];

      $risksMatrix = ReportHistory::select('sau_rm_report_histories.*')
        ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
        ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
        ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
        ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
        ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN')
        ->inRisks($this->filters['risks'], $this->filters['filtersType']['risks'])
        ->where("year", $this->filters['year'])
        ->where("month", $this->filters['month'])
        ->where('sau_rm_report_histories.company_id', $this->company_id);

      $risksMatrix->company_scope = $this->company_id;
      $risksMatrix = $risksMatrix->get();

      $matriz_calification = $this->getMatrixReport();

      $data = $matriz_calification ? $matriz_calification : [];

      $table_report = [];

      foreach ($risksMatrix as $keyMatrix => $itemMatrix)
      {
          $frec = -1;
          $imp = -1;
          $array_table = [];

          $array_table['process'] = $itemMatrix->process;
          $array_table['area'] = $itemMatrix->area;

          $qualifications = json_decode($itemMatrix->qualification, true);
          
          foreach ($qualifications as $keyQ => $itemQ)
          {
              if ($itemQ["name"] == 'Frecuencia Residual')
                  $frec = $itemQ["value"];

              if ($itemQ["name"] == 'Impacto Residual')
                  $imp = $itemQ["value"];
          }

          $array_table['sequence'] = $itemMatrix->risk_sequence;
          $array_table['color'] = $data[$frec][$imp]['color'];
          $array_table['risk_name'] = $itemMatrix->risk;

          array_push($table_report, $array_table);
      } 

      $this->data = [
        "title" => 'Listado de Riesgos con Frecuencia e Impacto Residual',
        "data" => $table_report,
        "confLocation" => $this->confLocation
      ];

      Sheet::macro('getColors', function (Sheet $sheet) use ($table_report) {
        return $table_report;
      });
    }

    public static function afterSheet(AfterSheet $event)
    {
      $report_colors = $event->sheet->getColors();

      $colors = [];

      foreach ($report_colors as $index => $color)
      {
        $number = $index + 3;

        if ($color['color'] == 'warning')
          $colors['E'.$number] = 'FFD950';

        if ($color['color'] == 'orange')
          $colors['E'.$number] = 'ef7b38';

        if ($color['color'] == 'success')
          $colors['E'.$number] = '02BC77';

        if ($color['color'] == 'primary')
          $colors['E'.$number] = 'f0635f';
      }

      foreach ($colors as $cols => $color)
      {
        $event->sheet->styleCells(
          $cols,
            [
              'fill' => [
                  'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                  'rotation' => 90,
                  'startColor' => [
                      'rgb' => $color
                  ],
                  'endColor' => [
                      'rgb' => $color
                  ],
                ],
            ]
        );
      }

      $event->sheet->styleCells(
        'A1:AZ2',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
              'wrapText' => true
            ],
            'font' => [
                'name' => 'Arial', 
                'bold' => true,
            ]
          ]
      );

      $event->sheet->styleCells(
        'A3:AZ2000',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
              'wrapText' => true
            ],
            'font' => [
                'name' => 'Arial', 
                'bold' => false,
            ]
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Mapa Riesgos Residuales Tabla';
    }

    public function view(): View
    {
      $keyword = KeywordManager::getKeywords($this->company_id);

      return view('exports.IndustrialSecure.RiskMatrix.riskMatrixReportTable', [
        'data' => $this->data,
        'keyword' => $keyword
      ]);
    }
}
