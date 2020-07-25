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
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class InspectionCompletExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;
    use LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $keywords;
    protected $items_id;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);
    }

    public function query()
    {
      $inspection = Inspection::selectRaw("
        sau_ph_inspections.id,
        sau_ph_inspections.name,
        sau_ph_inspections.state,
        sau_ph_inspections.created_at,
        sau_ph_inspection_sections.name as section_name,
        sau_ph_inspection_section_items.description as description,
        sau_ct_qualifications.name as qualification_name,
        sau_ct_qualifications.description as qualification_description,
        
        sau_employees_regionals.name AS regional,
        sau_employees_headquarters.name AS sede,
        sau_employees_processes.name AS proceso,
        sau_employees_areas.name AS area,
        
        r.name as regionals,
        h.name as headquarter,
        p.name as process,
        a.name as areas,
        sau_users.name as qualifier,
        sau_ph_inspection_items_qualification_area_location.find,
        sau_ph_inspection_items_qualification_area_location.qualification_date,
        sau_action_plans_activities.description as desc_plan,
        sau_action_plans_activities.execution_date,
        sau_action_plans_activities.expiration_date,
        sau_action_plans_activities.state as state_activity,
        u.name as responsible
      ")
      ->join('sau_ph_inspection_sections', function ($join) 
      {
        $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->join('sau_ph_inspection_section_items', function ($join) 
      {
        $join->on("sau_ph_inspection_section_items.inspection_section_id", "sau_ph_inspection_sections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_ph_inspection_regional', function ($join) 
      {
        $join->on("sau_ph_inspection_regional.inspection_id", "sau_ph_inspections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_ph_inspection_headquarter', function ($join) 
      {
        $join->on("sau_ph_inspection_headquarter.inspection_id", "sau_ph_inspections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_ph_inspection_process', function ($join) 
      {
        $join->on("sau_ph_inspection_process.inspection_id", "sau_ph_inspections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_ph_inspection_area', function ($join) 
      {
        $join->on("sau_ph_inspection_area.inspection_id", "sau_ph_inspections.id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_employees_regionals', function ($join) 
      {
        $join->on("sau_employees_regionals.id", "sau_ph_inspection_regional.employee_regional_id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
      ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_process.employee_process_id')
      ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
      ->leftJoin('sau_ph_inspection_items_qualification_area_location', 'sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
      ->leftJoin( 'sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
      ->leftJoin('sau_employees_regionals AS r', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id', 'r.id')
      ->leftJoin('sau_employees_headquarters AS h', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', 'h.id')
      ->leftJoin('sau_employees_processes AS p', 'sau_ph_inspection_items_qualification_area_location.employee_process_id', 'p.id')
      ->leftJoin('sau_employees_areas AS a', 'sau_ph_inspection_items_qualification_area_location.employee_area_id', 'a.id')
      ->leftJoin('sau_users', 'sau_ph_inspection_items_qualification_area_location.qualifier_id', 'sau_users.id')
      ->leftJoin('sau_action_plans_activity_module', function ($join) 
      {
        $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
        $join->on("sau_action_plans_activity_module.item_table_name", "=", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
      }) 
      ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
      ->leftJoin('sau_users AS u', 'u.id', 'sau_action_plans_activities.responsible_id')
      ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
      ->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals'])
      ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
      ->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes'])
      ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
      ->betweenDate($this->filters["dates"])
      ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description', 'qualification_name', 'qualification_description', 'regionals', 'areas', 'headquarter', 'process', 'qualifier', 'sau_ph_inspection_items_qualification_area_location.find', 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'sau_action_plans_activities.id', 'desc_plan', 'sau_action_plans_activities.execution_date', 'sau_action_plans_activities.expiration_date', 'state_activity', 'responsible')
      ->withoutGlobalScopes()
      ->where('sau_ph_inspections.company_id', $this->company_id);
      
      $result = DB::table(DB::raw("({$inspection->toSql()}) AS t"))
      ->selectRaw("
        id,
        name,
        GROUP_CONCAT(regional) AS regional,
        GROUP_CONCAT(sede) AS sede,
        GROUP_CONCAT(proceso) AS proceso,
        GROUP_CONCAT(area) AS area,
        created_at,
        state,
        section_name,
        description,
        qualification_name,
        qualification_description,
        qualifier,
        find,
        regionals,
        headquarter,
        process,
        areas,
        qualification_date,
        responsible,
        desc_plan,
        execution_date,
        expiration_date,
        state_activity
      ")
      ->groupBy('id', 'name', 'created_at', 'state', 'section_name', 'description', 'qualification_name', 'qualification_description', 'qualifier', 'find', 'regionals', 'areas', 'headquarter', 'process', 'qualification_date', 'responsible', 'desc_plan', 'execution_date', 'expiration_date', 'state_activity')
      ->orderBy('id')
      ->mergeBindings($inspection->getQuery());

      return $result;
    }

    public function query2()
    {
      $inspection = Inspection::selectRaw("
          sau_ph_inspections.*,
          GROUP_CONCAT(DISTINCT sau_employees_regionals.name ORDER BY sau_employees_regionals.name ASC) AS regional,
          GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS sede,
          GROUP_CONCAT(DISTINCT sau_employees_processes.name ORDER BY sau_employees_processes.name ASC) AS proceso,
          GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area,
          sau_ph_inspection_sections.name as section_name,
          sau_ph_inspection_section_items.description as description,
          sau_ct_qualifications.name as qualification_name,
          sau_ct_qualifications.description as qualification_description,
          r.name as regionals, 
          h.name as headquarter,
          p.name as process, 
          a.name as areas, 
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
        ->leftJoin('sau_ph_inspection_regional', 'sau_ph_inspection_regional.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_process', 'sau_ph_inspection_process.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_regional.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_process.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
        ->leftJoin('sau_ph_inspection_items_qualification_area_location', 'sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->leftJoin( 'sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->leftJoin('sau_employees_regionals AS r', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id', 'r.id')
        ->leftJoin('sau_employees_headquarters AS h', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', 'h.id')
        ->leftJoin('sau_employees_processes AS p', 'sau_ph_inspection_items_qualification_area_location.employee_process_id', 'p.id')
        ->leftJoin('sau_employees_areas AS a', 'sau_ph_inspection_items_qualification_area_location.employee_area_id', 'a.id')
        ->leftJoin('sau_users', 'sau_ph_inspection_items_qualification_area_location.qualifier_id',  'sau_users.id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) 
        {
            $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
            $join->on("sau_action_plans_activity_module.item_table_name", "=", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
        }) 
        ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
        ->leftJoin( 'sau_users AS u', 'u.id', 'sau_action_plans_activities.responsible_id')
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
        ->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals'])
        ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
        ->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes'])
        ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
        ->betweenDate($this->filters["dates"])
        ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description', 'sau_action_plans_activities.id', 'qualification_name', 'qualification_description', 'regionals', 'areas', 'headquarter', 'process', 'qualifier',  'sau_ph_inspection_items_qualification_area_location.find', 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'responsible', 'desc_plan', 'sau_action_plans_activities.execution_date', 'sau_action_plans_activities.expiration_date', 'state_activity');

      $inspection->company_scope = $this->company_id;

      return $inspection;
    }

    public function map($data): array
    {
      $values = [$data->id, $data->name];

      if ($this->confLocation['regional'] == 'SI')
        array_push($values, $data->regional);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($values, $data->sede);

      if ($this->confLocation['process'] == 'SI')
        array_push($values, $data->proceso);

      if ($this->confLocation['area'] == 'SI')
        array_push($values, $data->area);

      $values = array_merge($values, [
        $data->created_at,
        $data->state,
        $data->section_name,
        $data->description,
        $data->qualification_name,
        $data->qualification_description,
        $data->qualifier,
        $data->find
      ]);

      if ($this->confLocation['regional'] == 'SI')
        array_push($values, $data->regionals);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($values, $data->headquarter);

      if ($this->confLocation['process'] == 'SI')
        array_push($values, $data->process);

      if ($this->confLocation['area'] == 'SI')
        array_push($values, $data->areas);

      $values = array_merge($values, [
        $data->qualification_date,
        $data->desc_plan,
        $data->responsible,
        $data->expiration_date,
        $data->execution_date,
        $data->state_activity
      ]);

      return $values;
    }

    public function headings(): array
    {
      $columns = ['Código', 'Nombre'];

      if ($this->confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocation['process'] == 'SI')
        array_push($columns, $this->keywords['process']);

      if ($this->confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area']);
      
      $columns = array_merge($columns, [
        'Fecha de creación',
        '¿Activa?',
        'Nombre del tema',
        'Descripción del item',
        'Calificación',
        'Descripción Calificación',
        'Calificador',
        'Hallazgo'
      ]);

      if ($this->confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional'].' calificado(a)');

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter'].' calificado(a)');

      if ($this->confLocation['process'] == 'SI')
        array_push($columns, $this->keywords['process'].' calificado(a)');

      if ($this->confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area'].' calificado(a)');

      $columns = array_merge($columns, [
        'Fecha calificación',
        'Descripción de la actividad del plan de acción',
        'Responsable',
        'Fecha de vencimiento',
        'Fecha de ejecución',
        'Estado'
      ]);

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
        return 'Inspecciones';
    }
}

