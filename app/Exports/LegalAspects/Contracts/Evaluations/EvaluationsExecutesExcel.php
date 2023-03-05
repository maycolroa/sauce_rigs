<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\TypeRating;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationsExecutesExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $evaluators;
    protected $interviewees;
    protected $qualifications;
    protected $ratings;
    protected $evaluation_contract_id;

    public function __construct($company_id, $filters, $evaluation_contract_id = NULL)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->evaluation_contract_id = $evaluation_contract_id;

      $evaluators = EvaluationContract::selectRaw('
        sau_ct_evaluation_contract.id as id, 
        GROUP_CONCAT(sau_users.name) as evaluators')
        ->join('sau_ct_evaluation_user', 'sau_ct_evaluation_user.evaluation_id', 'sau_ct_evaluation_contract.id')
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluation_user.user_id')
        ->groupBy('sau_ct_evaluation_contract.id');

      $this->checkId($evaluators);

      $evaluators->company_scope = $company_id;
      $this->evaluators = $evaluators->pluck('evaluators', 'id');

      $interviewees = EvaluationContract::selectRaw('
        sau_ct_evaluation_contract.id as id, 
        GROUP_CONCAT(CONCAT("(Nombre: ", sau_ct_interviewees.name, " / Cargo: ", sau_ct_interviewees.position, ")")) as interviewees')
        ->join('sau_ct_interviewees', 'sau_ct_interviewees.evaluation_id', 'sau_ct_evaluation_contract.id')
        ->groupBy('sau_ct_evaluation_contract.id');

      $this->checkId($interviewees);

      $interviewees->company_scope = $company_id;
      $this->interviewees = $interviewees->pluck('interviewees', 'id');

      $qualifications = EvaluationContract::selectRaw('
        CONCAT(sau_ct_evaluation_contract.id, "_", sau_ct_evaluation_item_rating.item_id, "_", sau_ct_evaluation_item_rating.type_rating_id) AS indice,
        sau_ct_evaluation_item_rating.value')
        ->join('sau_ct_evaluation_item_rating', 'sau_ct_evaluation_item_rating.evaluation_id', 'sau_ct_evaluation_contract.id');

      $this->checkId($qualifications);

      $qualifications->company_scope = $company_id;
      $this->qualifications = $qualifications->pluck('value', 'indice');

      $ratings = TypeRating::orderBy('id');
      $ratings->company_scope = $company_id;
      $this->ratings = $ratings->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      $evaluations = EvaluationContract::selectRaw(
        'sau_ct_evaluation_contract.id as evaluation_contract_id,
          sau_ct_evaluations.name as name,
          sau_ct_evaluations.type as type,
          sau_ct_evaluations.created_at as created_at,
          sau_ct_objectives.description as objective,
          sau_ct_evaluation_contract_objective_observations.observation as observation_objective,
          sau_ct_subobjectives.description as subobjective,
          sau_ct_items.description as item,
          sau_ct_items.id as item_id,
          CONCAT(sau_ct_information_contract_lessee.nit, " - ", sau_ct_information_contract_lessee.social_reason) as contract,
          sau_ct_evaluation_contract.evaluation_date as evaluation_date,
          sau_users.name as evaluator,
          sau_ct_item_observations.description as observation'
        )
        //->join('sau_users', 'sau_users.id', 'sau_ct_evaluations.creator_user_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id')
        ->join('sau_ct_evaluations', 'sau_ct_evaluations.id', 'sau_ct_evaluation_contract.evaluation_id')
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
        ->join('sau_ct_items', 'sau_ct_items.subobjective_id', 'sau_ct_subobjectives.id')
        ->leftJoin('sau_ct_item_observations', function($q) {
            $q->on('sau_ct_item_observations.item_id', '=', 'sau_ct_items.id')
              ->on('sau_ct_evaluation_contract.id', '=', 'sau_ct_item_observations.evaluation_id');
        })
        ->leftJoin('sau_ct_evaluation_contract_objective_observations', function($q) {
            $q->on('sau_ct_evaluation_contract_objective_observations.objective_id', '=', 'sau_ct_objectives.id')
              ->on('sau_ct_evaluation_contract.id', '=', 'sau_ct_evaluation_contract_objective_observations.evaluation_id');
        })
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluation_contract.evaluator_id');

        if (COUNT($this->filters) > 0)
        {
          $evaluations->inObjectives($this->filters['objectives'], $this->filters['filtersType']['evaluationsObjectives']);
          $evaluations->inSubobjectives($this->filters['subobjectives'], $this->filters['filtersType']['evaluationsSubobjectives']);

          if (COUNT($this->filters["dates"]) > 0)
          {            
            $evaluations->betweenDate($this->filters["dates"]);
          }
        }
        
        //->orderBy('name, objective, subobjective, item', 'ASC');

      $this->checkId($evaluations);

      $evaluations->company_scope = $this->company_id;

      return $evaluations->get();
    }

    public function map($data): array
    {
      $values = [
        $data->evaluation_contract_id.$data->item_id,
        $data->name,
        $data->type,
        //$data->user_creator,
        $data->created_at,
        $data->objective,
        $data->observation_objective,
        $data->subobjective,
        $data->item,
        $data->contract,
        $this->evaluators[$data->evaluation_contract_id],
        isset($this->interviewees[$data->evaluation_contract_id]) ? $this->interviewees[$data->evaluation_contract_id] : '',
        $data->evaluation_date,
        $data->evaluator,
        $data->observation
      ];

      $key = $data->evaluation_contract_id.'_'.$data->item_id.'_';

      foreach ($this->ratings as $rating)
      {
        $value = isset($this->qualifications[$key.$rating->id]) ? 
          ($this->qualifications[$key.$rating->id] ? 
              $this->qualifications[$key.$rating->id] : 'N/A')
          : 'N/A';

        $value = str_replace('pending', 'NO', $value);

        array_push($values, $value);
      }

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Código evaluación',
        'Nombre',
        'Tipo',
        //'Usuario creador',
        'Fecha de creación',
        'Tema',
        'Observación Tema',
        'Subtema',
        'Item',
        'Contratista',
        'Evaluadores',
        'Entrevistados',
        'Fecha de calificación',
        'Calificador',
        'Observaciones'
      ];

      foreach ($this->ratings as $rating)
      {
        array_push($columns, $rating->name);
      }

      return $columns;
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
        return 'Evaluaciones calificadas';
    }

    private function checkId($query)
    {
      if ($this->evaluation_contract_id)
        return $query->where('sau_ct_evaluation_contract.id', $this->evaluation_contract_id);
    }
}

