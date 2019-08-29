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

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class CheckEmpresarialExcel implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $data;

    public function __construct($company_id, $data)
    {
      $this->company_id = $company_id;
      $this->data = $data;
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
        $regional = $regional->find($employee->employee_position_id)->name;
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
        ($employee->afp ? $employee->afp->name : ''),
        ($employee->arl ? $employee->arl->name : ''),
        $employee->contract_numbers,
        $employee->last_contract_date,
        $employee->contract_type,
        $data->disease_origin,
        $data->eps_favorability_concept,
        $data->cie10Code->code,
        $data->cie10Code->description,
        $data->cie10Code->system,
        $data->cie10Code->category,
        $data->laterality,
        $data->has_recommendations,
        $data->start_recommendations,
        $data->indefinite_recommendations,
        $data->end_recommendations,
        $data->relocated,
        $data->monitoring_recommendations,
        $data->origin_recommendations,
        $data->detail,
        $data->has_restrictions,
        $data->start_restrictions,
        $data->indefinite_restrictions,
        $data->end_restrictions,
        $restriction,
        $data->has_incapacitated,
        $data->incapacitated_days,
        $data->incapacitated_last_extension,
        $data->case_classification,
        $data->next_date_tracking,
        $data->in_process_origin,
        $data->process_origin_done,
        $data->process_origin_done_date,
        $data->emitter_origin,
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
        'Fecha Inicio',
        'Identificación',
        'Nombre Trabajador',
        'Fecha de nacimiento',
        'Sexo',
        'Fecha de ingreso a la empresa',
        'Antigüedad',
        'Edad',
        'Cargo',
        'Centro de costos',
        'Regional',
        'EPS',
        'AFP',
        'ARL',
        'Números de contrato',
        'Fecha último contrato',
        'Tipo contrato',
        'Origen de enfermedad',
        'Concepto de favorabilidad EPS',
        'Código CIE10',
        'Descripción CIE10',
        'Sistema',
        'Categoría',
        'Lateralidad',
        '¿Tiene recomendaciones?',
        'Fecha Inicio Recomendaciones',
        '¿Recomendacions indefinidas?',
        'Fecha Fin Recomendaciones',
        '¿Reubidado?',
        'Fecha de seguimiento a recomendaciones',
        'Procedencia de las recomendaciones',
        'Detalle',
        '¿Tiene Restricción?',
        'Fecha inicio restricción',
        '¿Restricción indefinida?',
        'Fecha fin restricción',
        'Parte del cuerpo afectada',
        '¿Incapacitado?',
        'Números de dias',
        'Última prórroga',
        'Clasificación del caso',
        'Fecha próximo seguimiento',
        '¿En proceso de calificación de origen?',
        '¿Ya se hizo el proceso de calificación de origen?',
        'Fecha proceso calificación origen',
        'Entidad que Califica Origen',
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
            'J' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER,
            'AO' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'R' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AD' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AF' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AJ' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AL' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AP' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AR' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AU' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AY' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:BA1',
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

