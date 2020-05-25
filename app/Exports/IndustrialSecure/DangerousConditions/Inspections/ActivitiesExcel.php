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
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
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

/**
select 
`sau_ph_inspections`.*,
 GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS sede,
 GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area,
 `sau_ph_inspection_sections`.`name` as `section_name`,
 `sau_ph_inspection_section_items`.`description` as `description`,
`sau_ct_qualifications`.`name` as `qualification_name`,
 `sau_ct_qualifications`.`description` as `qualification_description`,
 `a`.`name` as `areas`, 
 `h`.`name` as `headquarter`,
 `sau_users`.`name` as `qualifier`,
 `q`.`find`, 
 `q`.`qualification_date`, 
 sau_action_plans_activities.description as desc_plan, 
 sau_action_plans_activities.execution_date,
 sau_action_plans_activities.expiration_date,
`sau_action_plans_activities`.`state` as `state_activity`,
`u`.`name` as `responsible`
 from `sau_ph_inspections` 
 inner join `sau_ph_inspection_sections` on `sau_ph_inspection_sections`.`inspection_id` = `sau_ph_inspections`.`id` 
 inner join `sau_ph_inspection_section_items` on `sau_ph_inspection_section_items`.`inspection_section_id` = `sau_ph_inspection_sections`.`id` 
 left join `sau_ph_inspection_headquarter` on `sau_ph_inspection_headquarter`.`inspection_id` = `sau_ph_inspections`.`id` 
 left join `sau_ph_inspection_area` on `sau_ph_inspection_area`.`inspection_id` = `sau_ph_inspections`.`id` 
 left join `sau_employees_headquarters` on `sau_employees_headquarters`.`id` = `sau_ph_inspection_headquarter`.`employee_headquarter_id` 
 left join `sau_employees_areas` on `sau_employees_areas`.`id` = `sau_ph_inspection_area`.`employee_area_id` 
 left join sau_ph_inspection_items_qualification_area_location q on q.item_id = sau_ph_inspection_section_items.id
 left join `sau_ct_qualifications` on `sau_ct_qualifications`.`id` = `q`.`qualification_id`
 left join sau_employees_headquarters h on q.employee_headquarter_id = h.id
 left join sau_employees_areas a on q.employee_area_id = a.id 
 left join `sau_users` on `q`.`qualifier_id` = `sau_users`.`id` 
 left join `sau_action_plans_activity_module` on sau_action_plans_activity_module.item_id = q.id  and item_table_name = 'sau_ph_inspection_items_qualification_area_location'
 left join sau_action_plans_activities on sau_action_plans_activities.id = sau_action_plans_activity_module.activity_id
left join sau_users u on u.id = sau_action_plans_activities.responsible_id
 where `sau_ph_inspections`.`company_id` = 1 
 group by `sau_ph_inspections`.`id`, `sau_ph_inspections`.`name`, `sau_ph_inspections`.`created_at`, `sau_ph_inspections`.`state`, `sau_ph_inspection_sections`.`name`, `sau_ph_inspection_section_items`.`description`, sau_action_plans_activities.id, qualification_name, qualification_description, areas, headquarter, qualifier,  `q`.`find`, `q`.`qualification_date`, responsible, desc_plan, sau_action_plans_activities.execution_date, sau_action_plans_activities.expiration_date, state_activity
 order by `sau_ph_inspections`.`id`
 **/
