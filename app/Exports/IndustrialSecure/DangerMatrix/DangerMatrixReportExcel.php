<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use \Maatwebsite\Excel\Sheet;

use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Traits\DangerMatrixTrait;
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixReportExcel implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;
    use DangerMatrixTrait;
    use LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $data;
    protected $confLocation;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);

      $data = [];
      $title_table = '';
      $data_table = [];

      $conf = QualificationCompany::select('qualification_id');
      $conf->company_scope = $this->company_id;
      $conf = $conf->first();

      if ($conf && $conf->qualification)
        $conf = $conf->qualification->name;

      if ($conf)
      {
        $data = $this->getMatrixCalification($conf);

        $dangersMatrix = DangerMatrix::select('sau_dangers_matrix.*')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
            ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
            ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
            ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
            ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
            ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN');
            //->inMatrix($this->filters['matrix'], $this->filters['filtersType']['matrix']);

        $dangersMatrix->company_scope = $this->company_id;
        $dangersMatrix = $dangersMatrix->get();

        foreach ($dangersMatrix as $keyMatrix => $itemMatrix)
        {
          foreach ($itemMatrix->activities as $keyActivity => $itemActivity)
          {
            $activity_dangers = $itemActivity->dangers()->inDangers($this->filters['dangers'], $this->filters['filtersType']['dangers'])->get();

            foreach ($activity_dangers as $keyDanger => $itemDanger)
            {
              $nri = -1;
              $ndp = -1;

              foreach ($itemDanger->qualifications as $keyQ => $itemQ)
              {
                if ($conf == 'Tipo 1')
                {
                  if ($itemQ->typeQualification->description == 'NRI')
                    $nri = $itemQ->value_id;

                  if ($itemQ->typeQualification->description == 'Nivel de Probabilidad')
                    $ndp = $itemQ->value_id;
                }
              }

              if ($conf == 'Tipo 1')
                if (isset($data[$ndp]) && isset($data[$ndp][$nri]))
                  $data[$ndp][$nri]['count']++;
            }
          }
        }

        $matriz = [];
        $headers = array_keys($data);
        $count = isset($data['Ha ocurrido en el sector Hospitalario']) ? COUNT($data['Ha ocurrido en el sector Hospitalario']) : 0;

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

        if ($this->filters['row'])
        {
          $dangers = DangerMatrix::select(
              'sau_dangers_matrix.id AS id',
              'sau_dm_dangers.name AS name',
              'sau_dm_activity_danger.danger_description AS danger_description',
              'sau_employees_regionals.name as regional',
              'sau_employees_headquarters.name as headquarter',
              'sau_employees_processes.name as process',
              'sau_employees_areas.name as area',
              'sau_employees_processes.types as types'
          )
          ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
          ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
          ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
          ->join('sau_dm_qualification_danger', 'sau_dm_qualification_danger.activity_danger_id', 'sau_dm_activity_danger.id')
          ->join('sau_dm_qualification_types', 'sau_dm_qualification_types.id', 'sau_dm_qualification_danger.type_id')
          ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_dangers_matrix.employee_regional_id')
          ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_dangers_matrix.employee_headquarter_id')
          ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
          ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_dangers_matrix.employee_area_id')
          ->inRegionals($this->filters['regionals'], isset($this->filters['filtersType']['regionals']) ? $this->filters['filtersType']['regionals'] : 'IN')
            ->inHeadquarters($this->filters['headquarters'], isset($this->filters['filtersType']['headquarters']) ? $this->filters['filtersType']['headquarters'] : 'IN')
            ->inAreas($this->filters['areas'], isset($this->filters['filtersType']['areas']) ? $this->filters['filtersType']['areas'] : 'IN')
            ->inProcesses($this->filters['processes'], isset($this->filters['filtersType']['processes']) ? $this->filters['filtersType']['processes'] : 'IN')
            ->inMacroprocesses($this->filters['macroprocesses'], isset($this->filters['filtersType']['macroprocesses']) ? $this->filters['filtersType']['macroprocesses'] : 'IN')
          //->inMatrix($this->filters['matrix'], $this->filters['filtersType']['matrix'])
          ->inDangers($this->filters['dangers'], $this->filters['filtersType']['dangers'])
          ->where('sau_dm_activity_danger.qualification', $this->filters['label'])
          ->where('sau_dm_qualification_types.description', 'Nivel de Probabilidad')
          ->where('sau_dm_qualification_danger.value_id', $this->filters['row']);

          $dangers->company_scope = $this->company_id;
          $data_table = $dangers->get();

          $title_table = "Peligros ".$this->filters['label']." de (".$this->filters['row'].")";
        }
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

      $event->sheet->styleCells(
        'A10:O12',
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
        'A13:O200',
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
      return 'Reporte de Matriz de peligros';
    }

    public function view(): View
    {
      return view('exports.IndustrialSecure.DangerMatrix.dangerMatrixReport', [
          'data' => $this->data
      ]);
    }
}
