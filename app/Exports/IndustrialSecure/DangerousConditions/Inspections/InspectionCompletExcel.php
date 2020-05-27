<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
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
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class InspectionCompletExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;
    protected $filters;
    protected $keywords;
    protected $items_id;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->keywords = $this->getKeywordQueue($this->company_id);
    }

    public function query()
    {
      $inspection = Inspection::selectRaw("
          sau_ph_inspections.*,
          GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS sede,
          GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area,
          sau_ph_inspection_sections.name as section_name,
          sau_ph_inspection_section_items.description as description,
          sau_ct_qualifications.name as qualification_name,
          sau_ct_qualifications.description as qualification_description,
          a.name as areas, 
          h.name as headquarter,
          sau_users.name as qualifier,
          sau_ph_inspection_items_qualification_area_location.find, 
          sau_ph_inspection_items_qualification_area_location.qualification_date, 
          sau_action_plans_activities.description as desc_plan, 
          sau_action_plans_activities.execution_date,
          sau_action_plans_activities.expiration_date,
          sau_action_plans_activities.state as state_activity,
          u.name as responsible"
        )
        ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
        ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->leftJoin('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
        ->leftJoin('sau_ph_inspection_items_qualification_area_location', 'sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->leftJoin( 'sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->leftJoin('sau_employees_headquarters AS h', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', 'h.id')
        ->leftJoin( 'sau_employees_areas AS a', 'sau_ph_inspection_items_qualification_area_location.employee_area_id', 'a.id' )
        ->leftJoin( 'sau_users', 'sau_ph_inspection_items_qualification_area_location.qualifier_id',  'sau_users.id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) 
        {
            $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
            $join->on("sau_action_plans_activity_module.item_table_name", "=", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
        }) 
        ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
        ->leftJoin( 'sau_users AS u', 'u.id', 'sau_action_plans_activities.responsible_id')
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
        ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
        ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
        ->betweenDate($this->filters["dates"])
        ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description', 'sau_action_plans_activities.id', 'qualification_name', 'qualification_description', 'areas', 'headquarter', 'qualifier',  'sau_ph_inspection_items_qualification_area_location.find', 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'responsible', 'desc_plan', 'sau_action_plans_activities.execution_date', 'sau_action_plans_activities.expiration_date', 'state_activity');

      $inspection->company_scope = $this->company_id;

      return $inspection;
    }

    public function map($data): array
    {
      return [
        $data->id,
        $data->name,
        $data->sede,
        $data->area,
        $data->created_at,
        $data->state,
        $data->section_name,
        $data->description,
        $data->qualification_name,
        $data->qualification_description,
        $data->qualifier,
        $data->find,
        $data->headquarter,
        $data->areas,
        $data->qualification_date,
        $data->desc_plan,
        $data->responsible,
        $data->expiration_date,
        $data->execution_date,
        $data->state_activity
      ];
    }

    public function headings(): array
    {
        return [
          'Código',
          'Nombre',
          $this->keywords['headquarter'],
          $this->keywords['area'],
          'Fecha de creación',
          '¿Activa?',
          'Nombre del tema',
          'Descripción del item',
          'Calificación',
          'Descripción Calificación',
          'Calificador',
          'Hallazgo',          
          $this->keywords['headquarter'].' calificado(a)',
          $this->keywords['area'].' calificado(a)',
          'Fecha calificación',
          'Descripción de la actividad del plan de acción',
          'Responsable',
          'Fecha de vencimiento',
          'Fecha de ejecución',
          'Estado'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:T1',
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
        return 'Inspecciones';
    }
}

