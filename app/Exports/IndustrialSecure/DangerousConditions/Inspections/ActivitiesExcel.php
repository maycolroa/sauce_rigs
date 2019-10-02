<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
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

class ActivitiesExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $items_id;
    protected $module_id;

    public function __construct($company_id, $filters)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
        $this->module_id = Module::where('name', 'dangerousConditions')->first()->id;

        $inspectionsReady = InspectionItemsQualificationAreaLocation::select(
            'sau_ph_inspection_items_qualification_area_location.id AS id'
        )
        ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_sections.id',  'sau_ph_inspection_section_items.inspection_section_id')
        ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
        ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
        ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
        ->betweenInspectionDate($this->filters["dates"])
        ->where('sau_ph_inspections.company_id', $this->company_id)
        ->pluck('id')
        ->toArray();

        $this->items_id = $inspectionsReady;
    }

    public function query()
    {
        $activities = ActionPlansActivity::select(
            'sau_action_plans_activities.*',
            'sau_action_plans_activities.state as state_activity',
            'sau_users.name as responsible',
            'sau_action_plans_activity_module.item_id AS item_id'
        )
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
        ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
        ->where('item_table_name', 'sau_ph_inspection_items_qualification_area_location')
        ->where('sau_action_plans_activity_module.module_id', $this->module_id)
        ->whereIn('sau_action_plans_activity_module.item_id', $this->items_id);

        $activities->company_scope = $this->company_id;

        return $activities;
    }

    public function map($data): array
    {
        return [
            $data->description,
            $data->responsible,
            $data->item_id,
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
            'Codigo inspeccion calificadas',
            'Fecha de vencimiento',
            'Fecha de ejecución',
            'Estado'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:F1',
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

