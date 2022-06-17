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
      $this->confLocationTableInspections = $this->getLocationFormConfTableInspections($this->company_id);

    }

    public function query()
    {
        $select = [];
        $having = [];

        $select[] = "sau_ph_inspections.id";
        $select[] = "sau_ph_inspections.name";
        $select[] = "sau_ph_inspections.state";
        $select[] = "sau_ph_inspections.created_at";
        $select[] = "sau_ph_inspection_sections.name as section_name";
        $select[] = "sau_ph_inspection_section_items.description as description";

        if ($this->confLocationTableInspections['regional'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(r.name) 
                FROM sau_ph_inspection_regional ri 
                INNER JOIN sau_employees_regionals r ON r.id = ri.employee_regional_id
                WHERE ri.inspection_id = sau_ph_inspections.id
            ) AS regional";

        if ($this->confLocationTableInspections['headquarter'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(h.name) 
                FROM sau_ph_inspection_headquarter hi 
                INNER JOIN sau_employees_headquarters h ON h.id = hi.employee_headquarter_id
                WHERE hi.inspection_id = sau_ph_inspections.id
            ) AS sede";

        if ($this->confLocationTableInspections['process'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(p.name) 
                FROM sau_ph_inspection_process pi
                INNER JOIN sau_employees_processes p ON p.id = pi.employee_process_id
                WHERE pi.inspection_id = sau_ph_inspections.id
            ) AS proceso";

        if ($this->confLocationTableInspections['area'] == 'SI')
            $select[] = " (SELECT GROUP_CONCAT(ar.name) 
                FROM sau_ph_inspection_area a 
                INNER JOIN sau_employees_areas ar ON ar.id = a.employee_area_id
                WHERE a.inspection_id = sau_ph_inspections.id
            ) AS area";

        $inspections = Inspection::groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description')
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
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
        ->betweenDate($this->filters["dates"])
        ->withoutGlobalScopes()
        ->where('sau_ph_inspections.company_id', $this->company_id);
 
      /*$inspection = Inspection::selectRaw("
        sau_ph_inspections.id,
        sau_ph_inspections.name,
        sau_ph_inspections.state,
        sau_ph_inspections.created_at,
        sau_ph_inspection_sections.name as section_name,
        sau_ph_inspection_section_items.description as description,
        
        sau_employees_regionals.name AS regional,
        sau_employees_headquarters.name AS sede,
        sau_employees_processes.name AS proceso,
        sau_employees_areas.name AS area
      ")*/
      
      /*->leftJoin('sau_ph_inspection_regional', function ($join) 
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
      ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')*/
      if (isset($this->filters['regionals']) && COUNT($this->filters['regionals']) > 0)
      {         
          $select[] = "(SELECT COUNT(rf.employee_regional_id) 
              FROM sau_ph_inspection_regional rf 
              WHERE rf.inspection_id = sau_ph_inspections.id
              and rf.employee_regional_id ".$this->filters['filtersType']['regionals']." (".implode(',', $this->getValuesForMultiselect($this->filters['regionals'])->toArray()).")
          ) AS regional2";    

          $having[] = "regional2 >= 1";
      }

      if (isset($this->filters["headquarters"]) && COUNT($this->filters["headquarters"]) > 0)
      {
          $select[] = "(SELECT COUNT(hf.employee_headquarter_id) 
              FROM sau_ph_inspection_headquarter hf 
              WHERE hf.inspection_id = sau_ph_inspections.id
              and hf.employee_headquarter_id ".$this->filters['filtersType']['headquarters']." (".implode(',', $this->getValuesForMultiselect($this->filters["headquarters"])->toArray()).")
          ) AS sede2";    

          $having[] = "sede2 >= 1";
      }

      if (isset($this->filters["processes"]) && COUNT($this->filters["processes"]) > 0)
      {
          $select[] = "(SELECT COUNT(pf.employee_process_id) 
              FROM sau_ph_inspection_process pf 
              WHERE pf.inspection_id = sau_ph_inspections.id
              and pf.employee_process_id ".$this->filters['filtersType']['processes']." (".implode(',', $this->getValuesForMultiselect($this->filters["processes"])->toArray()).")
          ) AS proceso2";    

          $having[] = "proceso2 >= 1";
      }
      
      if (isset($this->filters["areas"]) && COUNT($this->filters["areas"]) > 0)
      {
          $select[] = "(SELECT COUNT(af.employee_area_id) 
              FROM sau_ph_inspection_area af 
              WHERE af.inspection_id = sau_ph_inspections.id
              and af.employee_area_id  ".$this->filters['filtersType']['areas']." (".implode(',', $this->getValuesForMultiselect($this->filters["areas"])->toArray()).")
          ) AS area2"; 

          $having[] = "area2 >= 1";
      }
      /*->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals'])
      ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
      ->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes'])
      ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])*/

      $inspections->selectRaw(implode(",", $select));

      if (COUNT($having) > 0)
          $inspections->havingRaw(implode(" and ", $having));

      
      $select = [];
      $select2[] = "id";
      $select2[] = "name";
      $select2[] = "created_at";
      $select2[] = "state";
      $select2[] = "section_name";
      $select2[] = "description";

      if ($this->confLocationTableInspections['regional'] == 'SI')
            $select2[] = "GROUP_CONCAT(regional) AS regional";

        if ($this->confLocationTableInspections['headquarter'] == 'SI')
            $select2[] = "GROUP_CONCAT(sede) AS sede";

        if ($this->confLocationTableInspections['process'] == 'SI')
            $select2[] = "GROUP_CONCAT(proceso) AS proceso";

        if ($this->confLocationTableInspections['area'] == 'SI')
            $select2[] = "GROUP_CONCAT(area) AS area";
      
      $result = DB::table(DB::raw("({$inspections->toSql()}) AS t"))
      ->selectRaw(implode(",", $select2))
      ->groupBy('id', 'name', 'created_at', 'state', 'section_name', 'description')
      ->orderBy('id')
      ->mergeBindings($inspections->getQuery());

      return $result;
    }

    public function map($data): array
    {
      $values = [$data->id, $data->name];

      if ($this->confLocationTableInspections['regional'] == 'SI')
        array_push($values, $data->regional);

      if ($this->confLocationTableInspections['headquarter'] == 'SI')
        array_push($values, $data->sede);

      if ($this->confLocationTableInspections['process'] == 'SI')
        array_push($values, $data->proceso);

      if ($this->confLocationTableInspections['area'] == 'SI')
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

      if ($this->confLocationTableInspections['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($this->confLocationTableInspections['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocationTableInspections['process'] == 'SI')
        array_push($columns, $this->keywords['process']);

      if ($this->confLocationTableInspections['area'] == 'SI')
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
        return 'Inspecciones Planeadas';
    }
}

