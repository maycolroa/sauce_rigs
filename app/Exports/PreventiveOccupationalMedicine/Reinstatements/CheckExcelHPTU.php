<?php

namespace App\Exports\PreventiveOccupationalMedicine\Reinstatements;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use Datetime;

use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Business\EmployeeBusiness;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Restriction;
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class CheckExcelHPTU implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;
    protected $data;
    protected $keywords;

    public function __construct($company_id, $data)
    {
      $this->company_id = $company_id;
      $this->data = $data;
      $this->keywords = $this->getKeywordQueue($this->company_id);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function map($data): array
    {
      $employee = Employee::query();
      $employee->company_scope = $this->company_id;
      $employee = $employee->find($data->employee_id);

      $position = $business = $regional = $restriction = '';

      if ($employee->employee_position_id)
      {
        $position = EmployeePosition::query();
        $position->company_scope = $this->company_id;
        $position = $position->find($employee->employee_position_id)->name;
      }
      
      if ($employee->employee_business_id)
      {
        $business = EmployeeBusiness::query();
        $business->company_scope = $this->company_id;
        $business = $business->find($employee->employee_business_id)->name;
      }
      
      if ($employee->employee_regional_id)
      {
        $regional = EmployeeRegional::query();
        $regional->company_scope = $this->company_id;
        $regional = $regional->find($employee->employee_regional_id)->name;
      }

      if ($data->restriction_id)
      {
        $restriction = Restriction::query();
        $restriction->company_scope = $this->company_id;
        $restriction = $restriction->find($data->restriction_id)->name;
      }

      //\Log::info($data);
      return [
        $data->id,
        $data->state,
        $data->deadline,
        $data->motive_close,
        Carbon::createFromFormat('D M d Y', $data->created_at)->format('Y-m-d'),
        $employee->identification,
        $employee->name,
        $employee->date_of_birth,
        $employee->sex,
        $employee->income_date,
        $this->timeDifference($employee->income_date),
        ($employee->date_of_birth ? $this->timeDifference((Carbon::createFromFormat('Y-m-d', $employee->date_of_birth))->toDateString()) : ''),
        $position,
        $business,
        $regional,
        ($employee->eps ? $employee->eps->name : ''),
        $data->disease_origin,
        $data->cie10Code->code,
        $data->cie10Code->description,
        $data->cie10Code->system,
        $data->cie10Code->category,
        $data->laterality,
        $data->has_recommendations,
        $data->start_recommendations,
        $data->indefinite_recommendations,
        $data->end_recommendations,
        $data->has_incapacitated ? $data->has_incapacitated : 'NO',
        $data->start_incapacitated,
        $data->end_incapacitated,
        $data->relocated,
        $data->monitoring_recommendations,
        $data->origin_recommendations,
        $data->refund_classification,
        $data->detail,
        $data->has_restrictions,
        $restriction,
        $data->reinforced_job_stability,
        $data->in_process_origin,
        $data->process_origin_done,
        $data->process_origin_done_date,
        $data->emitter_origin,
        $data->qualification_origin,
        $data->in_process_pcl,
        $data->process_pcl_done,
        $data->process_pcl_done_date,
        $data->pcl,
        $data->entity_rating_pcl
      ];
    }

    public function headings(): array
    {
      return [
        'ID Reporte',
        'Estado',
        'Fecha de cierre',
        'Motivo de cierre',
        'Fecha creación reporte',
        'Identificación',
        'Nombre Trabajador',
        'Fecha de nacimiento',
        'Sexo',
        'Fecha de ingreso a la empresa',
        'Antigüedad',
        'Edad',
        $this->keywords['position'],
        $this->keywords['businesses'],
        $this->keywords['regional'],
        $this->keywords['eps'],
        $this->keywords['disease_origin'],
        'Código CIE10',
        'Descripción CIE10',
        'Sistema',
        'Categoría',
        'Lateralidad',
        '¿Tiene recomendaciones?',
        'Fecha Inicio Recomendaciones',
        '¿Recomendacions indefinidas?',
        'Fecha Fin Recomendaciones',
        '¿Incapacitado?',
        'Fecha Inicio Incapacidad',
        'Fecha Fin Incapacidad',
        '¿Reubidado?',
        'Fecha de seguimiento a recomendaciones',
        'Procedencia de las recomendaciones',
        'Clasificación reintegro',
        $this->keywords['detail_recommendations'],
        '¿Tiene Restricción?',
        'Parte del cuerpo afectada',
        '¿Tiene estabilidad laboral reforzada?',
        '¿En proceso de calificación de origen?',
        '¿Ya se hizo el proceso de calificación de origen?',
        'Fecha proceso calificación origen',
        'Entidad que Califica Origen',
        'Clasificación de origen',
        '¿En proceso de PCL?',
        '¿Ya se hizo el proceso de PCL?',
        'Fecha proceso PCL',
        'Calificación PCL',
        'Entidad que califica PCL'
      ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'X' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Z' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AC' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AE' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AM' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AR' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
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
        return 'Reportes';
    }

    private function timeDifference($startDate, $endDate = null)
    {
      $start = new DateTime($startDate);
      $end;

      if ($endDate == null)
          $end = new DateTime();
      else
          $start = new DateTime($endDate);

      $interval = $start->diff($end);

      return $interval->format('%y años %m meses y %d dias');
    }
}

