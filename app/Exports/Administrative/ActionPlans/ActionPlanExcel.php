<?php

namespace App\Exports\Administrative\ActionPlans;

use App\Models\ActionPlansActivity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ActionPlanExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
{
    use RegistersEventListeners;

    protected $user;
    protected $company_id;
    protected $filters;
    protected $isSuperAdmin;
    protected $all_permissions;

    public function __construct($user, $company_id, $filters, $isSuperAdmin, $all_permissions)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->isSuperAdmin = $isSuperAdmin;
      $this->all_permissions = $all_permissions;
    }

    public function query()
    {
      $activities = ActionPlansActivity::select(
            'sau_action_plans_activities.*',
            'sau_action_plans_activities.state as state_activity',
            'sau_users.name as responsible',
            'sau_modules.display_name')
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
        ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
        ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
        ->inResponsibles($this->filters['responsibles'], $this->filters['filtersType']['responsibles'])
        ->inModules($this->filters['modules'], $this->filters['filtersType']['modules'])
        ->inStates($this->filters['states'], $this->filters['filtersType']['states']);
        
      if (!$this->isSuperAdmin)
      {
          $activities->where(function ($subquery) {
              $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->all_permissions);

              $subquery->orWhere('sau_action_plans_activities.responsible_id', $this->user->id);
          });
      }

      $activities->company_scope = $this->company_id;

      return $activities;
    }

    public function map($data): array
    {
      return [
        $data->description,
        $data->responsible,
        $data->expiration_date,
        $data->execution_date,
        $data->state_activity,
        $data->display_name
      ];
    }

    public function headings(): array
    {
        return [
          'Descripción',
          'Responsable',
          'Fecha de vencimiento',
          'Fecha de ejecución',
          'Estado',
          'Módulo'
        ];
    }

    public function columnFormats(): array
    {
        return [
          'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
          'D' => NumberFormat::FORMAT_DATE_DDMMYYYY
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
        return 'Planes de acción';
    }
}

