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

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class InspectionListExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    public function query()
    {
      $inspection = Inspection::select(
          'sau_ph_inspections.*',
          DB::raw('GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS sede'),
          DB::raw('GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area'),
          'sau_ph_inspection_sections.name AS section_name',
          'sau_ph_inspection_section_items.description AS description'
        )
        ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
        ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->leftJoin('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
        ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
        ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
        ->betweenDate($this->filters["dates"])
        ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name', 'sau_ph_inspections.created_at', 'sau_ph_inspections.state', 'sau_ph_inspection_sections.name', 'sau_ph_inspection_section_items.description');

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
        $data->description
      ];
    }

    public function headings(): array
    {
        return [
          'Código',
          'Nombre',
          'Sede',
          'Área',
          'Fecha de creación',
          '¿Activa?',
          'Nombre del tema',
          'Descripción del item'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:H1',
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

