<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
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
use App\Models\Administrative\Users\User;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;

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
    protected $id;
    protected $add_fields;
    protected $add_fields_values;

    public function __construct($company_id, $filters, $id, $user)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->id = $id;
      $this->user = $user;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);

      try
      {
          $this->configLevel = ConfigurationsCompany::company($this->company)->findByKey('filter_inspections');
      } catch (\Exception $e) {
          $this->configLevel = 'NO';
      }
    }

    public function query()
    {
      $qualifications = InspectionItemsQualificationAreaLocation::selectRaw("
          sau_ph_inspections.id as insp_id,
          sau_ph_inspections.name as insp_name,
          sau_ph_inspections.type_id AS type_inspection,
          sau_ph_inspection_sections.name as section_name,
          sau_ph_inspection_section_items.description as description,
          sau_ph_qualifications_inspections.name as qualification_name,
          sau_ph_qualifications_inspections.description as qualification_description,
          sau_users.name as qualifier,
          sau_ph_inspection_items_qualification_area_location.find, 
          sau_ph_inspection_items_qualification_area_location.qualification_date,
          sau_ph_inspection_items_qualification_area_location.qualify AS qualify_personalizada,
          sau_employees_regionals.name as regionals, 
          sau_employees_headquarters.name as headquarter,
          sau_employees_processes.name as process, 
          sau_employees_areas.name as areas,
          sau_action_plans_activities.description as desc_plan,
          sau_action_plans_activities.execution_date,
          sau_action_plans_activities.expiration_date,
          sau_action_plans_activities.state as state_activity,
          u.name as responsible"
        )
      ->leftJoin('sau_ph_inspection_section_items', 'sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
      ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
      ->join('sau_ph_inspections', 'sau_ph_inspections.id', 'sau_ph_inspection_sections.inspection_id')
      ->leftJoin( 'sau_ph_qualifications_inspections', 'sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
      ->leftJoin('sau_employees_regionals', function ($join) 
      {
        $join->on("sau_employees_regionals.id", "sau_ph_inspection_items_qualification_area_location.employee_regional_id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_employees_headquarters', function ($join) 
      {
        $join->on("sau_employees_headquarters.id", "sau_ph_inspection_items_qualification_area_location.employee_headquarter_id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_employees_processes', function ($join) 
      {
        $join->on("sau_employees_processes.id", "sau_ph_inspection_items_qualification_area_location.employee_process_id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_employees_areas', function ($join) 
      {
        $join->on("sau_employees_areas.id", "sau_ph_inspection_items_qualification_area_location.employee_area_id");
        $join->on("sau_ph_inspections.company_id", "=", DB::raw("{$this->company_id}"));
      })
      ->leftJoin('sau_users', 'sau_ph_inspection_items_qualification_area_location.qualifier_id',  'sau_users.id')
      ->leftJoin('sau_action_plans_activity_module', function ($join) 
      {
          $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
          $join->on("sau_action_plans_activity_module.item_table_name", "=", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
      }) 
      ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
      ->leftJoin( 'sau_users AS u', 'u.id', 'sau_action_plans_activities.responsible_id')
      ->InQualifiers($this->filters['qualifiers'], $this->filters['filtersType']['qualifiers'])
      ->betweenDate($this->filters["dates"]);

      if (isset($this->filters['themes']) && $this->filters['filtersType']['themes'] && COUNT($this->filters['themes']) > 0)
          $qualifications->inThemes($this->filters['themes'], $this->filters['filtersType']['themes']);

      if (isset($this->filters['inspections']) && $this->filters['filtersType']['inspections'] && COUNT($this->filters['inspections']) > 0)
          $qualifications->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections']);

      if (isset($this->filters['regionals']) && $this->filters['filtersType']['regionals'] && COUNT($this->filters['regionals']) > 0)
          $qualifications->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals']);

      if (isset($this->filters['headquarters']) && $this->filters['filtersType']['headquarters'] && COUNT($this->filters['headquarters']) > 0)
          $qualifications->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters']);
      
      if (isset($this->filters['processes']) && isset($this->filters['filtersType']['processes']) && COUNT($this->filters['processes']) > 0)
          $qualifications->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes']);

      if (isset($this->filters['areas']) && isset($this->filters['filtersType']['areas']) && COUNT($this->filters['areas']) > 0)
          $qualifications->inAreas($this->filters['areas'], $this->filters['filtersType']['areas']);

      $qualifications->where('sau_ph_inspections.id', $this->id);

      $qualifications->company_scope = $this->company_id;

      if ($this->configLevel == 'SI')
      {
          $locationLevelForm = ConfigurationsCompany::company($this->company_id)->findByKey('location_level_form_user_inspection_filter');

          if ($locationLevelForm)
          {
              $id = $this->user->id;
              if ($locationLevelForm == 'Regional')
              {                  
                $regionals = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();

                  if (count($regionals) > 0)
                      $qualifications->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id',$regionals);
              }
              else if ($locationLevelForm == 'Sede')
              {
                $regionals = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();
                  $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');

                  if (count($regionals) > 0 && count($headquarters) > 0)
                      $qualifications->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id',$headquarters)
                      ->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals);
              }
              else if ($locationLevelForm == 'Proceso')
              {
                  $$regionals = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();
                  $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                  $processes = User::find($this->user->id)->processes()->pluck('id');

                  if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0)
                      $qualifications->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id',$regionals)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id',$headquarters)
                      ->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id',$processes);
              }
              else if ($locationLevelForm == 'Área')
              {                        
                $regionals = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();
                  $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                  $processes = User::find($this->user->id)->processes()->pluck('id');
                  $areas = User::find($this->user->id)->areas()->pluck('id');

                  if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0 && count($areas) > 0)
                  {
                      $qualifications->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id',$regionals)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id',$headquarters)
                      ->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id',$processes)
                      ->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id',$areas);
                  }
              }
          }
      }

      return $qualifications;
    }

    public function map($data): array
    {
      $this->add_fields_values = AdditionalFieldsValues::select('value')->where('qualification_date', $data->qualification_date)->get();

      $values = [$data->insp_name];

      if ($data->type_inspection == 3)
      {
        $values = array_merge($values, [
          $data->section_name,
          $data->description,
          $data->qualify_personalizada,
          $data->qualify_personalizada,
          $data->qualifier,
          $data->find
        ]);
      }
      else
      {        
        $values = array_merge($values, [
          $data->section_name,
          $data->description,
          $data->qualification_name,
          $data->qualification_description,
          $data->qualifier,
          $data->find
        ]);
      }

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

      foreach ($this->add_fields_values as $key => $value2) {
        array_push($values, $value2->value);
      }

      return $values;
    }

    public function headings(): array
    {
      $this->add_fields = AdditionalFields::select('name')->where('inspection_id', $this->id)->get();

      $columns = ['Nombre'];
      
      $columns = array_merge($columns, [
        'Nombre del tema',
        'Descripción del item',
        'Calificación',
        'Descripción Calificación',
        'Calificador',
        'Observación'
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

      foreach ($this->add_fields as $key => $value) {
        array_push($columns, $value->name);
      }

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AZ1',
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
        return 'Inspec. Planeadas Calificadas';
    }
}

