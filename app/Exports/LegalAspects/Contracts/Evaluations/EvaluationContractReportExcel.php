<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationContractReportExcel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    public function collection()
    {
      $whereDates = '';

      $whereObjectives = $this->scopeQueryReport('o', $this->filters["objectives"], $this->filters['filtersType']['evaluationsObjectives']);

      $whereSubojectives = $this->scopeQueryReport('s', $this->filters["subobjectives"], $this->filters['filtersType']['evaluationsSubobjectives']);

      $whereQualificationTypes = $this->scopeQueryReport('eir', $this->filters["qualificationTypes"], $this->filters['filtersType']['qualificationTypes'], 'type_rating_id');
          
      $subWhereQualificationTypes = $this->scopeQueryReport('etr', $this->filters["qualificationTypes"], $this->filters['filtersType']['qualificationTypes'], 'type_rating_id');

      if (COUNT($this->filters["dates"]) > 0)
      {
        $whereDates = ' AND ec.evaluation_date BETWEEN "'.$this->filters['dates'][0].'" AND "'.$this->filters['dates'][1].'"';
      }

      $evaluations = DB::table(DB::raw("(SELECT 
          t.*,
          CONCAT(ROUND( (t_cumple * 100) / (t_cumple + t_no_cumple), 1), '%') AS p_cumple,
          CONCAT(ROUND( (t_no_cumple * 100) / (t_cumple + t_no_cumple), 1), '%') AS p_no_cumple
          FROM (

            SELECT 
                GROUP_CONCAT(DISTINCT s.id) as id,
                o.description as objective,
                s.description as subobjective,
                COUNT(DISTINCT ec.id) as t_evaluations,
                SUM(IF(eir.value = 'NO' OR eir.value = 'pending', 1, 0)) AS t_no_cumple,
                SUM(IF(eir.value = 'SI' OR eir.value = 'N/A', 1,
                        IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
                            IF(eir.value IS NULL AND eir.item_id IS NULL,
                                (SELECT 
                                        COUNT(etr.type_rating_id)
                                    FROM
                                        sau_ct_evaluation_type_rating etr
                                    WHERE
                                        etr.evaluation_id = e.id {$subWhereQualificationTypes}
                                )
                            , 0)
                        )
                    )
                ) AS t_cumple
            
                FROM sau_ct_evaluation_contract ec
                INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
                INNER JOIN sau_ct_items i ON i.subobjective_id = s.id
                LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id AND eir.evaluation_id = ec.id
            
                WHERE ec.company_id = ".$this->company_id. $whereDates . $whereObjectives . $whereSubojectives . $whereQualificationTypes ."
                GROUP BY objective, subobjective
            ) AS t
        ) AS t"))
        ->orderBy('objective');
        
      $result = collect([]);

      $totales = new \stdClass();
      $totales->objective = '';
      $totales->subobjective = 'TOTALES';
      $totales->t_evaluations = 0;
      $totales->t_cumple = 0;
      $totales->t_no_cumple = 0;
      $totales->p_cumple = 0;
      $totales->p_no_cumple = 0;

      foreach ($evaluations->get() as $value)
      {
        $result->push($value);

        $totales->t_evaluations = $totales->t_evaluations + $value->t_evaluations;
        $totales->t_cumple = $totales->t_cumple + $value->t_cumple;
        $totales->t_no_cumple = $totales->t_no_cumple + $value->t_no_cumple;
      }

      if ($totales->t_evaluations > 0)
      {
        $totales->p_cumple = $totales->t_cumple / ($totales->t_cumple + $totales->t_no_cumple);
        $totales->p_no_cumple = $totales->t_no_cumple / ($totales->t_cumple + $totales->t_no_cumple);
        $result->push($totales);
      }

      return $result;
    }

    public function map($data): array
    {
      return [
        $data->objective,
        $data->subobjective,
        $data->t_evaluations,
        $data->t_cumple,
        $data->t_no_cumple,
        $data->p_cumple,
        $data->p_no_cumple
      ];
    }

    public function headings(): array
    {
        return [
          'Tema',
          'Subtema',
          'Evaluaciones',
          '#Cumplimiento',
          '#No Cumplimiento',
          '%Cumplimiento',
          '%No Cumplimiento'
        ];
    }

    public function columnFormats(): array
    {
        return [
          'D' => NumberFormat::FORMAT_NUMBER,
          'E' => NumberFormat::FORMAT_NUMBER,
          'F' => NumberFormat::FORMAT_PERCENTAGE_00,
          'G' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:G1',
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
        return 'Evaluaciones - Reporte';
    }

    private function scopeQueryReport($table, $data, $typeSearch, $primary = 'id')
    {
      $ids = [];
      $query = '';

      foreach ($data as $key => $value)
      {
          $ids[] = $value;
      }

      if(COUNT($ids) > 0)
      {
          $ids = implode(",", $ids);

          if ($typeSearch == 'IN')
              $query = " AND $table.$primary IN ($ids)";

          else if ($typeSearch == 'NOT IN')
              $query = " AND $table.$primary NOT IN ($ids)";
      }

      return $query;
    }
}

