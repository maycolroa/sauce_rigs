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

      $where = 'iq2.employee_regional_id = sau_ph_inspection_items_qualification_area_location.employee_regional_id ';

      if ($this->confLocation)
      {
        if ($this->confLocation['headquarter'] == 'SI')
          $where .= 'AND iq2.employee_headquarter_id = sau_ph_inspection_items_qualification_area_location.employee_headquarter_id ';
        if ($this->confLocation['process'] == 'SI')
          $where .= 'AND iq2.employee_process_id = sau_ph_inspection_items_qualification_area_location.employee_process_id ';
        if ($this->confLocation['area'] == 'SI')
          $where .= 'AND iq2.employee_area_id = sau_ph_inspection_items_qualification_area_location.employee_area_id ';
      }

      $where .= 'AND i2.type_id = 2';

      if ($this->table == "with_theme" )
        $column = 's.name as section';
      else
        $column = 'l.name as headquarter';

      $consultas = InspectionItemsQualificationAreaLocation::select(
          'i.name as name',
          'r.name as regional',
          'l.name as headquarter',
          'p.name as process',
          'a.name as area',
          "{$column}",
          DB::raw("(
            select count(distinct iq2.qualification_date) from sau_ph_inspection_items_qualification_area_location iq2
            inner join sau_ph_inspection_section_items it2 on iq2.item_id = it2.id
            inner join sau_ph_inspection_sections s2 on it2.inspection_section_id = s2.id
            inner join sau_ph_inspections i2 on s2.inspection_id = i2.id
            where {$where}
            ) as numero_inspecciones"),
          DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
          DB::raw('count(IF(q.fulfillment = 1, q.id, null)) as numero_items_cumplimiento'),
          DB::raw('count(IF(q.fulfillment = 0, q.id, null)) as numero_items_no_cumplimiento'),
          DB::raw('count(IF(q.fulfillment = 2, q.id, null)) as numero_items_cumplimiento_parcial'),
          DB::raw("sum(
            (SELECT IF(COUNT(IF(iap2.state=\"Pendiente\",0, NULL)) > 0, 1, 0) 
            FROM sau_action_plans_activities iap2 
            inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
            WHERE 
            iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
            iam2.module_id = {$module_id} and
            iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
            )AS numero_planes_no_ejecutados"),
          DB::raw("sum(
            (SELECT IF(COUNT(true), 1, 0) as actividades_totales
            FROM sau_action_plans_activities iap2 
            inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
            WHERE 
            iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
            iam2.module_id = {$module_id} and
            iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
            )AS actividades_totales")
        )
        ->join('sau_ph_inspection_section_items as it','sau_ph_inspection_items_qualification_area_location.item_id', 'it.id')
        ->join('sau_ph_inspection_sections as s','it.inspection_section_id', 's.id')
        ->join('sau_ph_inspections as i', function ($join) 
        {
            $join->on("s.inspection_id", "i.id");
            $join->on("i.type_id", DB::raw(2));
        })
        //->join('sau_ph_inspections as i','s.inspection_id', 'i.id')
        ->join('sau_ph_qualifications_inspections as q','q.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->leftJoin('sau_employees_regionals as r', 'r.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
        ->leftJoin('sau_employees_headquarters as l','l.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
        ->leftJoin('sau_employees_processes as p', 'p.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
        ->leftJoin('sau_employees_areas as a','a.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
        ->where('i.company_id', $this->company_id)
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'], 'i')
        ->betweenDate($this->filters["dates"])
        ->inThemes($this->filters["themes"], $this->filters['filtersType']['themes'], 's');

        if (isset($this->filters['regionals']) && COUNT($this->filters['regionals']) > 0)
            $consultas->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals']);

        if (isset($this->filters['headquarters']) && COUNT($this->filters['headquarters']) > 0)
            $consultas->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters']);
        
        if (isset($this->filters['processes']) && COUNT($this->filters['processes']) > 0)
            $consultas->inProcesses($this->filters['processes'], $this->filters['filtersType']['processes']);

        if (isset($this->filters['areas']) && COUNT($this->filters['areas']) > 0)
            $consultas->inAreas($this->filters['areas'], $this->filters['filtersType']['areas']);

        if ($this->table == "with_theme")
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'numero_inspecciones', 'section');
        else
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'numero_inspecciones');

      $consultas = $consultas->get();

      $consultas->transform(function($item, $key) {
        
        $item->porcentaje_items_cumplimiento = ($item->numero_items > 0) ? round(($item->numero_items_cumplimiento / $item->numero_items) * 100, 1)."%" : '0%';

        $item->porcentaje_items_no_cumplimiento = ($item->numero_items > 0) ? round(($item->numero_items_no_cumplimiento / $item->numero_items) * 100, 1)."%" : '0%';

        $item->porcentaje_items_cumplimiento_parcial = ($item->numero_items > 0) ? round(($item->numero_items_cumplimiento_parcial / $item->numero_items) * 100, 1)."%" : '0%';

        $item->numero_planes_ejecutados = $item->actividades_totales - $item->numero_planes_no_ejecutados;

        return $item;
      });
        
      /*$result = collect([]);

      $totales = new \stdClass();
      $totales->objective = '';
      $totales->subobjective = 'TOTALES';
      $totales->t_evaluations = 0;
      $totales->t_cumple = 0;
      $totales->t_no_cumple = 0;
      $totales->p_cumple = 0;
      $totales->p_no_cumple = 0;

      foreach ($evaluations->get() as $value)
      {
        $result->push($value);

        $totales->t_evaluations = $totales->t_evaluations + $value->t_evaluations;
        $totales->t_cumple = $totales->t_cumple + $value->t_cumple;
        $totales->t_no_cumple = $totales->t_no_cumple + $value->t_no_cumple;
      }

      if ($totales->t_evaluations > 0)
      {
        $totales->p_cumple = $totales->t_cumple / ($totales->t_cumple + $totales->t_no_cumple);
        $totales->p_no_cumple = $totales->t_no_cumple / ($totales->t_cumple + $totales->t_no_cumple);
        $result->push($totales);
      }*/

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
        return 'Inspecciones Planeadas Tipo 2 - Reporte';
    }
}

