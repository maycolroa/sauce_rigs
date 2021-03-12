<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Reports;

use App\Models\General\Module;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
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

class ReportListExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use LocationFormTrait;
    use UtilsTrait;

    protected $company_id;
    protected $filters;
    protected $module_id;
    protected $keywords;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->module_id = Module::where('name', 'dangerousConditions')->first()->id;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);
    }

    public function query()
    {
      $report = Report::select(
          'sau_ph_reports.*',
          'sau_employees_regionals.name AS regional',
          'sau_employees_headquarters.name AS headquarter',
          'sau_employees_processes.name AS process',
          'sau_employees_areas.name AS area',
          'sau_users.name as user',
          'sau_users.document as document',
          'sau_ph_conditions.description as condition',
          'sau_ph_conditions_types.description as type',
          'sau_action_plans_activities.description as desc_plan',
          'sau_action_plans_activities.execution_date',
          'sau_action_plans_activities.expiration_date',
          'sau_action_plans_activities.state as state_activity',
          DB::raw('u.name as responsible')
        )
        ->join('sau_users', 'sau_users.id', 'sau_ph_reports.user_id')
        ->join('sau_ph_conditions', 'sau_ph_conditions.id', 'sau_ph_reports.condition_id')
        ->join('sau_ph_conditions_types', 'sau_ph_conditions_types.id', 'sau_ph_conditions.condition_type_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_reports.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_reports.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_reports.employee_area_id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) 
        {
            $join->on("sau_action_plans_activity_module.item_id", "sau_ph_reports.id");
            $join->on("sau_action_plans_activity_module.item_table_name", "=", DB::raw("'sau_ph_reports'"));
        }) 
        ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
        ->leftJoin( 'sau_users AS u', 'u.id', 'sau_action_plans_activities.responsible_id')
        ->inConditions($this->filters['conditions'], $this->filters['filtersType']['conditions'])
        ->inConditionTypes($this->filters['conditionTypes'], $this->filters['filtersType']['conditionTypes'])
        ->betweenDate($this->filters["dates"]);

        if (COUNT($this->filters['states']) > 0)
        {
            $report
            ->where('sau_action_plans_activity_module.module_id', $this->module_id)
            ->inStates($this->filters['states'], $this->filters['filtersType']['states'])
            ->groupBy('sau_ph_reports.id', 'headquarter', 'sau_ph_reports.created_at', 'user', 'condition', 'type', 'sau_ph_reports.rate', 'desc_plan', 'execution_date', 'expiration_date', 'state_activity', 'responsible');
        }

      $report->company_scope = $this->company_id;

      return $report;
    }

    public function map($data): array
    {
      $values = [$data->id];

      if ($this->confLocation['regional'] == 'SI')
        array_push($values, $data->regional);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($values, $data->headquarter);

      if ($this->confLocation['process'] == 'SI')
        array_push($values, $data->process);

      if ($this->confLocation['area'] == 'SI')
        array_push($values, $data->area);

      $values = array_merge($values, [
        $data->rate,
        $data->observation,
        $data->condition,
        $data->type,
        $data->other_condition,
        $data->user,
        $data->document,
        $data->created_at,
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
      $columns = ['Código'];

      if ($this->confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocation['process'] == 'SI')
        array_push($columns, $this->keywords['process']);

      if ($this->confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area']);

      $columns = array_merge($columns, [
        'Severidad',
        'Observación',
        'Condición',
        'Tipo de condición',
        'Otra Condición',
        'Usuario que Reporta',
        'Identificación',
        'Fecha de creación',
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
        'A1:R1',
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
      return 'Inspecciones no planeadas';
    }
}

