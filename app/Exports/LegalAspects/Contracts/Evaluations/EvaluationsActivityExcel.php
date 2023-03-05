<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\General\Module;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationsActivityExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $items_id;
    protected $module_id;
    protected $evaluation_contract_id;

    public function __construct($company_id, $filters, $evaluation_contract_id = NULL)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
        $this->evaluation_contract_id = $evaluation_contract_id;
        $this->module_id = Module::where('name', 'contracts')->first()->id;
    }

    public function query()
    {
        $activities = EvaluationContract::select(
            'sau_action_plans_activities.*',
            'sau_action_plans_activities.state as state_activity',
            'sau_users.name as responsible',
            'sau_action_plans_activity_module.item_id AS item_id',
            'sau_ct_items.description AS item',
            'sau_ct_evaluation_contract.id as evaluation_contract_id'
        )
        ->join('sau_ct_evaluation_contract_items', 'sau_ct_evaluation_contract_items.evaluation_id', 'sau_ct_evaluation_contract.id')
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.item_id', 'sau_ct_evaluation_contract_items.id')
        ->join('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
        ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id')
        ->join('sau_ct_items', 'sau_ct_items.id', 'sau_ct_evaluation_contract_items.item_id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.id', 'sau_ct_items.subobjective_id')
        ->join('sau_ct_objectives', 'sau_ct_objectives.id', 'sau_ct_subobjectives.objective_id')
        ->where('sau_action_plans_activity_module.module_id', $this->module_id);

        $activities->company_scope = $this->company_id;

        if (COUNT($this->filters) > 0)
        {
          $activities->inObjectives($this->filters['objectives'], $this->filters['filtersType']['evaluationsObjectives']);
          $activities->inSubobjectives($this->filters['subobjectives'], $this->filters['filtersType']['evaluationsSubobjectives']);

          if (COUNT($this->filters["dates"]) > 0)
          {            
            $activities->betweenDate($this->filters["dates"]);
          }
        }

        if ($this->evaluation_contract_id)
        {
          $activities->where('sau_ct_evaluation_contract_items.evaluation_id', $this->evaluation_contract_id);
        }

        return $activities;
    }

    public function map($data): array
    {
        $values = [
            $data->evaluation_contract_id.$data->item_id,
            $data->description,
            $data->responsible,
            $data->item,
            $data->expiration_date,
            $data->execution_date,
            $data->state_activity
        ];

        

        return $values;
    }

    public function headings(): array
    {
        return [
            'Código evaluación',
            'Descripción',
            'Responsable',
            'item',
            'Fecha de vencimiento',
            'Fecha de ejecución',
            'Estado'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:Z1',
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
        return 'Actividades';
    }
}

