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
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Traits\RiskMatrixTrait;
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class RiskMatrixReportResidualExcel implements FromView, WithEvents, WithTitle
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
      $title_table = '';
      $data_table = [];

      $data = $this->getMatrixReport();

      $risksMatrix = RiskMatrix::select('sau_rm_risks_matrix.*')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
        ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
        ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
        ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
        ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN')
        ->where('sau_rm_risks_matrix.company_id', $this->company_id);

      $risksMatrix->company_scope = $this->company_id;
      $risksMatrix = $risksMatrix->get();

      foreach ($risksMatrix as $keyMatrix => $itemMatrix)
      {
        foreach ($itemMatrix->subprocesses as $keySub => $itemSub)
        {
          $subprocess_risks = $itemSub->risks()->inRisks($this->filters['risks'], $this->filters['filtersType']['risks'])->get();

          foreach ($subprocess_risks as $keyRisk => $itemRisk)
          {
              $frec = $itemRisk->description_residual_frequency;

              $imp = $itemRisk->description_residual_impact;

              if (isset($data[$frec]) && isset($data[$frec][$imp]))
                      $data[$frec][$imp]['count']++;
          }
        }
      }

      $matriz = [];
      $headers = array_keys($data);

      $count = isset($data['Muy Baja']) ? COUNT($data['Muy Baja']) : 0;

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

      if ($this->filters['rowR'])
      {
        $risks = RiskMatrix::select(
          'sau_rm_risks_matrix.id AS id',
          'sau_rm_risk.name AS name',
          'sau_rm_risk.category AS category',
          'sau_employees_regionals.name as regional',
          'sau_employees_headquarters.name as headquarter',
          'sau_employees_processes.name as process',
          'sau_employees_areas.name as area',
          'sau_tags_processes.name as types'
        )
        ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
        ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
        ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
        ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
        ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
        ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
        ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN')
        ->where('sau_rm_subprocess_risk.description_residual_frequency',$this->filters['rowR'])
        ->where('sau_rm_subprocess_risk.description_residual_impact',$this->filters['colR']);

        $risks->company_scope = $this->company_id;
        $data_table = $risks->get();

        $title_table = "Riesgos ".$this->filters['rowR']." - ".$this->filters['colR']."";
      }

      $this->data = [
          "data1" => [
            "headers" => $headers,
            "data" => $matriz
          ],
          "data2" => [
            "title" => $title_table,
            "data" => $data_table,
            "confLocation" => $this->confLocation
          ]
      ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $colors['D5:F5'] = 'FFD950';
      $colors['G5:I5'] = 'ef7b38';
      $colors['J5:R5'] = 'f0635f';

      $colors['D6:I6'] = 'FFD950';
      $colors['J6:L6'] = 'ef7b38';
      $colors['M6:R6'] = 'f0635f';

      $colors['D7:F7'] = '02BC77';
      $colors['G7:L7'] = 'FFD950';
      $colors['M7:O7'] = 'ef7b38';
      $colors['P7:R7'] = 'f0635f';

      $colors['D8:I8'] = '02BC77';
      $colors['J8:O8'] = 'FFD950';
      $colors['P8:R8'] = 'ef7b38';

      $colors['D9:L9'] = '02BC77';
      $colors['M9:R9'] = 'FFD950';

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
        'A5:A9',
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
        'D5:R9',
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

      $event->sheet->styleCells(
        'A11:O13',
          [
            'font' => [
              'name' => 'Arial',
              'bold' => true,
              'size' => 11
            ],
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
          ]
      );

      $event->sheet->styleCells(
        'A14:O200',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
              'wrapText' => true
            ],
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Mapa Riesgos Residuales';
    }

    public function view(): View
    {
      $keyword = KeywordManager::getKeywords($this->company_id);
      $showLabelCol = true;

        return view('exports.IndustrialSecure.RiskMatrix.riskMatrixReport', [
          'data' => $this->data,
          'keyword' => $keyword,
          'showLabelCol' => $showLabelCol
        ]);
    }
}
