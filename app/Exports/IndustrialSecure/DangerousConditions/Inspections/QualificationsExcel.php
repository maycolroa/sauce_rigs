<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
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

class QualificationsExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
        $inspectionsReady = InspectionItemsQualificationAreaLocation::select(
            'sau_ph_inspections.name AS inspection_name',
            'sau_ph_inspections.id AS inspection_id',
            'sau_ph_inspection_items_qualification_area_location.id',
            'sau_ph_inspections.created_at AS inpection_created_at',
            'sau_ph_inspections.state AS inspection_state',
            'sau_ph_inspection_sections.name AS section_name',
            'sau_ph_inspection_section_items.description AS item_description',
            'sau_ct_qualifications.name AS qualification_name',
            'sau_ct_qualifications.description AS qualification_description',
            'sau_employees_areas.name AS area',
            'sau_employees_headquarters.name AS headquarter',
            'sau_users.name AS qualifier',
            'sau_ph_inspection_items_qualification_area_location.find',
            'sau_ph_inspection_items_qualification_area_location.qualification_date'
        )
        ->join('sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_sections.id',  'sau_ph_inspection_section_items.inspection_section_id')
        ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
        ->join('sau_employees_headquarters', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', 'sau_employees_headquarters.id')
        ->join('sau_employees_areas', 'sau_ph_inspection_items_qualification_area_location.employee_area_id', 'sau_employees_areas.id')
        ->join('sau_users', 'sau_ph_inspection_items_qualification_area_location.qualifier_id', 'sau_users.id')
        ->inInspections($this->filters['inspections'], $this->filters['filtersType']['inspections'])
        ->inHeadquarters($this->filters['headquarters'], $this->filters['filtersType']['headquarters'])
        ->inAreas($this->filters['areas'], $this->filters['filtersType']['areas'])
        ->betweenInspectionDate($this->filters["dates"])
        ->where('sau_ph_inspections.company_id', $this->company_id);

        return $inspectionsReady;
    }

    public function map($data): array
    {
      return [
        $data->id,
        $data->inspection_id,
        $data->inspection_name,
        $data->inpection_created_at,
        $data->inspection_state,
        $data->headquarter,
        $data->area,
        $data->section_name,
        $data->item_description,
        $data->qualification_name,
        $data->qualification_description,
        $data->qualifier,
        $data->find,
        $data->qualification_date
      ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Código inspección',
            'Nombre Inspección',
            'Fecha de creación Inspección',
            '¿Inspección Activa?',
            'Sede',
            'Área',
            'Nombre Tema',
            'Descripción item',
            'Calificación',
            'Descripción Calificación',
            'Calificador',
            'Hallazgo',
            'Fecha calificación'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:N1',
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
        return 'Inspecciones calificadas';
    }
}

