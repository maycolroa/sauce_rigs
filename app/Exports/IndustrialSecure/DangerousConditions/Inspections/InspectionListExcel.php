<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
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

class InspectionListExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;
    use LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $keywords;

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
        ssau_ph_inspections.id,
        sau_ph_inspections.name,
        sau_ph_inspections.state,
        sau_ph_inspections.created_at,
        sau_ph_inspection_sections.name as section_name,
        sau_ph_inspection_section_items.description as description,
        
        sau_employees_regionals.name AS regional,
        sau_employees_headquarters.name AS sede,
        sau_employees_processes.name AS proceso,
        sau_employees_areas.name AS area
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
      ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
      ->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals'])
      ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
      ->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes'])
      ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
      ->betweenDate($this->filters["dates"])
      ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description', 'regional', 'sede', 'proceso', 'area')
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
        description
      ")
      ->groupBy('id', 'name', 'created_at', 'state', 'section_name', 'description')
      ->orderBy('id')
      ->mergeBindings($inspection->getQuery());

      return $result;
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
        $data->description
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
        'Descripción del item'
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

