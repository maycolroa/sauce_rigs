<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use App\Models\LegalAspects\Contracts\Evaluation;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationsListExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $evaluation_contract_id;

    public function __construct($company_id, $filters, $evaluation_contract_id = NULL)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->evaluation_contract_id = $evaluation_contract_id;
    }

    public function query()
    {
      $evaluations = Evaluation::select(
          'sau_ct_evaluations.name as name',
          'sau_ct_evaluations.type as type',
          'sau_users.name as user_creator',
          'sau_ct_evaluations.created_at as created_at',
          'sau_ct_objectives.description as objective',
          'sau_ct_subobjectives.description as subobjective',
          'sau_ct_items.description as item'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluations.creator_user_id')
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
        ->join('sau_ct_items', 'sau_ct_items.subobjective_id', 'sau_ct_subobjectives.id')
        ->groupBy('name', 'type', 'user_creator', 'created_at', 'objective', 'subobjective', 'item')
        ->orderBy('name');

        if (COUNT($this->filters) > 0)
        {
          $evaluations->inObjectives($this->filters['objectives'], $this->filters['filtersType']['evaluationsObjectives']);
          $evaluations->inObjectives($this->filters['subobjectives'], $this->filters['filtersType']['evaluationsSubobjectives']);

          if (COUNT($this->filters["dates"]) > 0)
          {            
              $evaluations->join('sau_ct_evaluation_contract', 'sau_ct_evaluation_contract.evaluation_id', 'sau_ct_evaluations.id');
              $evaluations->betweenDate($this->filters["dates"]);
          }
        }

        if ($this->evaluation_contract_id)
        {
          $evaluationContract = EvaluationContract::where('id', $this->evaluation_contract_id);
          $evaluationContract->company_scope = $this->company_id;
          $evaluationContract = $evaluationContract->first();
          $evaluations->where('sau_ct_evaluations.id', $evaluationContract->evaluation_id);
        }

      $evaluations->company_scope = $this->company_id;

      return $evaluations;
    }

    public function map($data): array
    {
      return [
        $data->name,
        $data->type,
        $data->user_creator,
        $data->created_at,
        $data->objective,
        $data->subobjective,
        $data->item
      ];
    }

    public function headings(): array
    {
        return [
          'Nombre',
          'Tipo',
          'Usuario creador',
          'Fecha de creación',
          'Objetivo',
          'Subobjetivo',
          'Item'
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
        return 'Evaluaciones';
    }
}

