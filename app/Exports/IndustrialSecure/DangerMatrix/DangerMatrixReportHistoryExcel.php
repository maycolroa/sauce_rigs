<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use \Maatwebsite\Excel\Sheet;
use App\Models\IndustrialSecure\DangerMatrix\QualificationHistory;
use App\Models\IndustrialSecure\DangerMatrix\ReportHistory;
use App\Facades\Administrative\KeywordManager;


Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixReportHistoryExcel implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $result;
    protected $conf;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;

      $dangersMatrix = ReportHistory::select('sau_dm_report_histories.*')
            ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
            ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
            ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
            ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
            ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN')
            ->inDangers($this->filters['dangers'])
            ->inDangerDescription($this->filters['dangerDescription'])
            ->where("year", $this->filters['year'])
            ->where("month", $this->filters['month']);
      $dangersMatrix->company_scope = $this->company_id;
      $dangersMatrix = $dangersMatrix->get();

      if ($dangersMatrix->count() > 0)
      {
          $this->conf = $dangersMatrix[0]->type_configuration;
      }

      $matriz_calification = QualificationHistory::
        where("year", $this->filters['year'])
      ->where("month", $this->filters['month'])
      ->where("type_configuration", $this->conf)
      ->first();

      if ($matriz_calification)
          $matriz_calification = json_decode($matriz_calification->value, true);

      $data = $matriz_calification ? $matriz_calification : [];

      foreach ($dangersMatrix as $keyDanger => $itemDanger)
      {
        $nri = -1;
        $ndp = -1;
        $frec = -1;
        $sev = -1;

        $qualifications = json_decode($itemDanger->qualification, true);

        foreach ($qualifications as $keyQ => $itemQ)
        {
          if ($this->conf == 'Tipo 1')
          {
            if ($itemQ["name"] == 'NRI')
                $nri = $itemQ["value"];

            if ($itemQ["name"] == 'Nivel de Probabilidad')
                $ndp = $itemQ["value"];
          }
          else if ($this->conf == 'Tipo 2')
          {
              if ($itemQ["name"] == 'Frecuencia')
                  $frec = $itemQ["value"];

              if ($itemQ["name"] == 'Severidad')
                  $sev = $itemQ["value"];
          }
        }

        if ($this->conf == 'Tipo 1')
        {
            if (isset($data[$ndp]) && isset($data[$ndp][$nri]))
                $data[$ndp][$nri]['count']++;
        }
        else if ($this->conf == 'Tipo 2')
        {
            if (isset($data[$sev]) && isset($data[$sev][$frec]))
                $data[$sev][$frec]['count']++;
        }
      }

      $matriz = [];

      if ($this->conf == 'Tipo 1')
      { 
        $headers = array_keys($data);
        $count = isset($data['Ha ocurrido en el sector hospitalario']) ? COUNT($data['Ha ocurrido en el sector hospitalario']) : 0;

        for ($i=0; $i < $count; $i++)
        { 
          $y = 0;

          foreach ($data as $key => $value)
          {
            $x = 0;

            foreach ($value as $key2 => $value2)
            { 
              $matriz[$x][$y] = array_merge($data[$key][$key2], ["row"=>$key, "col"=>$key2]);
              $x++;
            }

            $y++;
          }
        }
      }
      else if ($this->conf == 'Tipo 2')
      {
          $showLabelCol = true;
          $headers = array_keys($data);
          $count = isset($data['MENOR']) ? COUNT($data['MENOR']) : 0;

          for ($i=0; $i < $count; $i++)
          { 
              $y = 0;

              foreach ($data as $key => $value)
              {
                  $x = 0;

                  foreach ($value as $key2 => $value2)
                  { 
                      $matriz[$x][$y] = array_merge($data[$key][$key2], ["row"=>$key, "col"=>$key2]);
                      $x++;
                  }

                  $y++;
              }
          }
      }

      $this->result = [
          "data1" => [
            "headers" => $headers,
            "data" => $matriz
          ],
          "data2" => [
            "title" => '',
            "data" => [],
            "confLocation" => []
          ]
      ];

      $conf = $this->conf;

      Sheet::macro('getConfDM', function (Sheet $sheet) use ($conf) {
        return $conf;
      });
    }

    public static function afterSheet(AfterSheet $event)
    {
      $conf = $event->sheet->getConfDM();

      if ($conf == 'Tipo 1')
      {
        $colors['A4:F4'] = 'ef7b38';
        $colors['G4:L4'] = 'f0635f';
        $colors['M4:O4'] = '6f42c1';

        $colors['A5:C5'] = 'FFD950';
        $colors['D5:I5'] = 'ef7b38';
        $colors['J5:O5'] = 'f0635f';

        $colors['A6:C6'] = '02BC77';
        $colors['D6:F6'] = 'FFD950';
        $colors['G6:L6'] = 'ef7b38';
        $colors['M6:O6'] = 'f0635f';

        $colors['A7:F7'] = '02BC77';
        $colors['G7:L7'] = 'FFD950';
        $colors['M7:O7'] = 'ef7b38';

        $colors['A8:I8'] = '02BC77';
        $colors['J8:O8'] = 'FFD950';

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
          'A1:AZ3',
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
          'A4:O8',
            [
              'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
              ],
              'font' => [
                'name' => 'Arial',
                'size' => 14
              ],
              'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
              ],
            ]
        );
      }
      else if ($conf == 'Tipo 2')
      {
        $colors['D4:F4'] = 'ffd950';
        $colors['G4:I4'] = 'ee7a38';
        $colors['J4:O4'] = 'f0635f';

        $colors['D5:F5'] = 'ffd950';
        $colors['G5:L5'] = 'ee7a38';
        $colors['M5:O5'] = 'f0635f';

        $colors['D6:F6'] = '29c3d7';
        $colors['G6:I6'] = 'ffd950';
        $colors['J6:O6'] = 'ee7a38';

        $colors['D7:I7'] = '29c3d7';
        $colors['J7:O7'] = 'ffd950';

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
          'A1:AZ3',
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
          'A4:A7',
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
          'D4:O7',
            [
              'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
              ],
              'font' => [
                'name' => 'Arial',
                'size' => 14
              ],
              'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
              ],
            ]
        );
      }
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Historico de Matriz de peligros';
    }

    public function view(): View
    {
      $keyword = KeywordManager::getKeywords($this->company_id);
      $showLabelCol = false;

      if ($this->conf == 'Tipo 2')
      {
        $showLabelCol = true;

        return view('exports.IndustrialSecure.DangerMatrix.dangerMatrixReportType2', [
          'data' => $this->result,
          'keyword' => $keyword,
          'showLabelCol' => $showLabelCol
        ]);
      }

      return view('exports.IndustrialSecure.DangerMatrix.dangerMatrixReport', [
          'data' => $this->result,
          'keyword' => $keyword,
          'showLabelCol' => $showLabelCol
      ]);
    }
}
