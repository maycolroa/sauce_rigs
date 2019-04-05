<?php

namespace App\Exports\LegalAspects\Evaluations;

use App\LegalAspects\Evaluation;
use App\LegalAspects\TypeRating;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EvaluationsListExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $company_id;

    public function __construct($company_id)
    {
      $this->company_id = $company_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      $evaluations = Evaluation::select(
          'sau_ct_evaluations.name as name',
          'sau_ct_evaluations.type as type',
          'sau_users.name as user_creator',
          'sau_ct_evaluations.created_at as created_at',
          'sau_ct_objectives.description as objective',
          'sau_ct_subobjectives.description as subobjective',
          'sau_ct_items.description as item',
          'sau_ct_items.id as item_id'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluations.creator_user_id')
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
        ->join('sau_ct_items', 'sau_ct_items.subobjective_id', 'sau_ct_subobjectives.id');

      $evaluations->company_scope = $this->company_id;

      return $evaluations->get();
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
        $data->item,
        'adada'
      ];
    }

    public function headings(): array
    {
      $columns = [
        'Nombre',
        'Tipo',
        'Usuario creador',
        'Fecha de creación',
        'Objetivo',
        'Subobjetivo',
        'Item'
      ];

      $ratings = TypeRating::orderBy('id');
      $ratings->company_scope = $this->company_id;

      foreach ($ratings->get() as $key => $value)
      {
        array_push($columns, $value->name);
      }

      return $columns;
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

