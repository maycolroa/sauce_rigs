<?php

namespace App\Exports\LegalAspects\Evaluations;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationContractReportExcel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
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

      if (COUNT($this->filters["dates"]) > 0)
      {
        $whereDates = ' AND ec.evaluation_date BETWEEN "'.$this->filters['dates'][0].'" AND "'.$this->filters['dates'][1].'"';
      }

      $evaluations = DB::table(DB::raw("(SELECT 
          t.*,
          (t_evaluations - t_no_cumple) AS t_cumple,
          ROUND( (t_evaluations - t_no_cumple) / t_evaluations, 1) AS p_cumple,
          ROUND( t_no_cumple / t_evaluations, 1) AS p_no_cumple 
          FROM (

            SELECT 
                GROUP_CONCAT(DISTINCT s.id) as id,
                o.description as objective,
                s.description as subobjective,
                COUNT(DISTINCT ec.id) as t_evaluations,
                SUM(
                (
                    SELECT IF(COUNT(IF(eir.value='NO',1, NULL)) > 0, 1, 0) as t_no_cumple
                        FROM sau_ct_items i
                        LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id
                        WHERE eir.evaluation_id = ec.id AND i.subobjective_id = s.id
                )) AS t_no_cumple
            
                FROM sau_ct_evaluation_contract ec
                INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
            
                WHERE ec.company_id = ".$this->company_id. $whereDates . $whereObjectives . $whereSubojectives ."
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
        $totales->p_cumple = $totales->t_cumple / $totales->t_evaluations;
        $totales->p_no_cumple = $totales->t_no_cumple / $totales->t_evaluations;
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
          'Objetivo',
          'Subobjetivo',
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

    private function scopeQueryReport($table, $data, $typeSearch)
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
              $query = " AND $table.id IN ($ids)";

          else if ($typeSearch == 'NOT IN')
              $query = " AND $table.id NOT IN ($ids)";
      }

      return $query;
    }
}

