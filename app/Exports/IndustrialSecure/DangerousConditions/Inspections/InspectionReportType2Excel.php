<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\General\Module;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;
use App\Traits\UtilsTrait;
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class InspectionReportType2Excel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents/*, WithColumnFormatting*/, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;
    use LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $table;
    protected $keywords;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->table = $this->filters['table'];
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);
    }

    public function collection()
    {
      $module_id = Module::where('name', 'dangerousConditions')->first()->id;

      if ($this->table == "with_theme" )
        $column = 'sau_ph_inspection_sections.name as section';
      else
        $column = 'sau_employees_headquarters.name as headquarter';

        $consultas = InspectionItemsQualificationAreaLocation::select(
          'sau_ph_inspections.name AS name',
          'sau_employees_regionals.name AS regional',
          'sau_employees_headquarters.name AS headquarter',
          'sau_employees_processes.name AS process',
          'sau_employees_areas.name AS area',
          "{$column}",
          DB::raw('COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS numero_inspecciones'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_no_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento_parcial'),
          DB::raw('
              CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
              THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 2)
              ELSE 0 END AS porcentaje_items_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
              THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
              ELSE 0 END AS porcentaje_items_no_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
          THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
          ELSE 0 END AS porcentaje_items_cumplimiento_parcial'),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados"),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados")
        )
        ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->join('sau_ph_inspections', function ($join) 
        {
          $join->on("sau_ph_inspections.company_id", DB::raw($this->company_id));
          $join->on("sau_ph_inspections.type_id", DB::raw(2));
          $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
        })
        ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
        ->leftJoin('sau_employees_headquarters','sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
        ->leftJoin('sau_employees_areas','sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
        {
          $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
          $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
          $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
        })
        ->leftJoin('sau_action_plans_activities', function ($join) 
        {
          $join->on("sau_action_plans_activities.company_id", DB::raw($this->company_id));
          $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
        })
        ->where('sau_ph_inspections.company_id', $this->company_id)
        ->betweenDate($this->filters["dates"]);

        if (isset($this->filters['inspections']) && COUNT($this->filters['inspections']) > 0)
            $consultas->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'], 'sau_ph_inspections');

        if (isset($this->filters['themes']) && COUNT($this->filters['themes']) > 0)
            $consultas->inThemes($this->filters['themes'], $this->filters['filtersType']['themes'], 'sau_ph_inspection_sections');

        if (isset($this->filters['regionals']) && COUNT($this->filters['regionals']) > 0)
            $consultas->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals']);

        if (isset($this->filters['headquarters']) && COUNT($this->filters['headquarters']) > 0)
            $consultas->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters']);
        
        if (isset($this->filters['processes']) && COUNT($this->filters['processes']) > 0)
            $consultas->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes']);

        if (isset($this->filters['areas']) && COUNT($this->filters['areas']) > 0)
            $consultas->inAreas($this->filters['areas'], $this->filters['filtersType']['areas']);

        if ($this->table == "with_theme")
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'section');
        else
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional');

      $consultas = $consultas->get();

      return $consultas;
    }

    public function map($data): array
    {
      $result = [$data->name];

      if ($this->confLocation['regional'] == 'SI')
        array_push($result, $data->regional);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($result, $data->headquarter);

      if ($this->confLocation['process'] == 'SI')
        array_push($result, $data->process);

      if ($this->confLocation['area'] == 'SI')
        array_push($result, $data->areas);

      if ($this->table == "with_theme")
        array_push($result, $data->section);

      $result = array_merge($result, [
        (string)$data->numero_inspecciones,
        (string)$data->numero_items_cumplimiento,
        (string)$data->numero_items_no_cumplimiento,
        (string)$data->numero_items_cumplimiento_parcial,
        $data->porcentaje_items_cumplimiento,
        $data->porcentaje_items_no_cumplimiento,
        $data->porcentaje_items_cumplimiento_parcial,
        (string)$data->numero_planes_ejecutados,
        (string)$data->numero_planes_no_ejecutados,
      ]);

      return $result;
    }

    public function headings(): array
    {
      $columns = ['Nombre'];

      if ($this->confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($this->confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocation['process'] == 'SI')
        array_push($columns, $this->keywords['process']);

      if ($this->confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area']);

      if ($this->table == "with_theme")
        array_push($columns, 'Tema');

      $columns = array_merge($columns, [
        '# Inspecciones',
        '# Items Cumplimiento',
        '# Items No Cumplimiento',
        '# Items Cumplimiento Parcial',
        '% Items Cumplimiento',
        '% Items No Cumplimiento',
        '% Items Cumplimiento Parcial',
        '# Planes de Acción Realizados',
        '# Planes de Acción No Realizados'
      ]);

      return $columns;
    }

    /*public function columnFormats(): array
    {
      if ($this->table == "with_theme")
      {
        return [
          'E' => NumberFormat::FORMAT_NUMBER,
          'F' => NumberFormat::FORMAT_NUMBER,
          'G' => NumberFormat::FORMAT_NUMBER,
          'H' => NumberFormat::FORMAT_PERCENTAGE_00,
          'I' => NumberFormat::FORMAT_PERCENTAGE_00,
          'J' => NumberFormat::FORMAT_NUMBER,
          'K' => NumberFormat::FORMAT_NUMBER,
        ];
      }
      else
      {
        return [
          'D' => NumberFormat::FORMAT_NUMBER,
          'E' => NumberFormat::FORMAT_NUMBER,
          'F' => NumberFormat::FORMAT_NUMBER,
          'G' => NumberFormat::FORMAT_PERCENTAGE_00,
          'H' => NumberFormat::FORMAT_PERCENTAGE_00,
          'I' => NumberFormat::FORMAT_NUMBER,
          'J' => NumberFormat::FORMAT_NUMBER,
        ];
      }
    }*/

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:K1',
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
        return 'Inspec Planeadas-Reporte Tipo 2';
    }
}

