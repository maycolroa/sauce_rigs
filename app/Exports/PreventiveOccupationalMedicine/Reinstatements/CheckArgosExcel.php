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

class CheckArgosExcel implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;
    protected $data;
    protected $keywords;
    protected $medicalMonitoring;
    protected $laborMonitoring;
    protected $tracing;

    public function __construct($company_id, $data, $medicalMonitoring, $laborMonitoring, $tracing)
    {
      $this->company_id = $company_id;
      $this->data = $data;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->medicalMonitoring = $medicalMonitoring->groupBy('check_id');
      $this->laborMonitoring = $laborMonitoring->groupBy('check_id');
      $this->tracing = $tracing->groupBy('check_id');
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
      $check = [
        $data->id,
        $data->state,
        $data->deadline,
        $data->motive_close,
        Carbon::createFromFormat('D M d Y', $data->created_at)->format('Y-m-d'),
        $employee->name,
        $employee->identification,
        $employee->date_of_birth,
        $employee->sex,
        ($employee->eps ? $employee->eps->name : ''),
        $employee->income_date,
        $business,
        $position,        
        $regional,
        $data->disease_origin,
        $data->cie10Code->category,
        $data->cie10Code->code,
        $data->cie10Code->description,
        $data->cie10Code->system,
        $data->has_recommendations,
        $data->start_recommendations,
        $data->indefinite_recommendations,
        $data->end_recommendations,
        $data->relocated,
        $data->origin_recommendations,
        $data->has_restrictions,
        $restriction,    
        $data->laterality,
        $data->detail,    
        $data->conclusion_recommendations,
        $data->in_process_origin,
        $data->process_origin_done,
        $data->process_origin_done_date,
        $data->emitter_origin,
        $data->in_process_pcl,
        $data->process_pcl_done,
        $data->process_pcl_done_date,
        $data->pcl
      ];

      $tracing = $this->tracing->get($data->id);
      $medicalMonitoring = $this->medicalMonitoring->get($data->id);
      $laborMonitoring = $this->laborMonitoring->get($data->id);

      //\Log::info($tracing);

      for ($i=0; $i < $this->medicalMonitoring->max()->count(); $i++) { 
        array_push($check, empty($medicalMonitoring[$i]) ? "" : $medicalMonitoring[$i]->set_at);
        array_push($check, empty($medicalMonitoring[$i]) ? "" : $medicalMonitoring[$i]->conclusion);
        array_push($check, empty($medicalMonitoring[$i]) ? "" : $medicalMonitoring[$i]->has_monitoring_content);
      }

      for ($i=0; $i < $this->laborMonitoring->max()->count(); $i++) { 
        array_push($check, empty($laborMonitoring[$i]) ? "" : $laborMonitoring[$i]->set_at);
        array_push($check, empty($laborMonitoring[$i]) ? "" : $laborMonitoring[$i]->conclusion);
        array_push($check, empty($laborMonitoring[$i]) ? "" : $laborMonitoring[$i]->has_monitoring_content);
        array_push($check, empty($laborMonitoring[$i]) ? "" : $laborMonitoring[$i]->productivity);
      }

      for ($i=0; $i < $this->tracing->max()->count(); $i++)
      { 
        array_push($check, empty($tracing[$i]) ? "" : $tracing[$i]->updated_at->toDateString());
        array_push($check, empty($tracing[$i]) ? "" : $tracing[$i]->madeBy->name);
        array_push($check, empty($tracing[$i]) ? "" : $tracing[$i]->description);
      }

      \Log::info($check);

      return $check;


    }

    public function headings(): array
    {
      $headers = [
        'ID Reporte',
        'Estado',
        'Fecha de cierre',
        'Motivo de cierre',
        'Fecha creación reporte',
        'Nombre Trabajador',
        'Identificación',
        'Fecha de nacimiento',
        'Género',
        $this->keywords['eps'],
        'Fecha de ingreso a la empresa',
        $this->keywords['businesses'],
        $this->keywords['position'],
        $this->keywords['regional'],
        $this->keywords['disease_origin'],
        'Categoría',
        'Código CIE10',
        'Descripción CIE10',
        'Sistema',
        '¿Tiene recomendaciones?',        
        'Fecha Inicio Recomendaciones',
        '¿Recomendacions indefinidas?',
        'Fecha Fin Recomendaciones',
        '¿Reubidado?',
        'Procedencia de las recomendaciones',
        '¿Tiene Restricción?',
        'Parte del cuerpo afectada',
        'Lateralidad',
        $this->keywords['detail_recommendations'],
        'Conclusión a recomendaciones',
        '¿En proceso de calificación de origen?',
        '¿Ya se hizo el proceso de calificación de origen?',
        'Fecha proceso calificación origen',
        'Entidad que Califica Origen',
        '¿En proceso de PCL?',
        '¿Ya se hizo el proceso de PCL?',
        'Fecha proceso PCL',
        'Calificación PCL',
      ];

      $maxCantTracing = $this->tracing->max()->count();
      $maxCantMedicalMonitoring = $this->medicalMonitoring->max()->count();
      $maxCantLaborMonitoring = $this->laborMonitoring->max()->count();

      for ($i=1; $i <= $maxCantMedicalMonitoring; $i++) { 
        array_push($headers,"Fecha Seguimiento Médico $i");
        array_push($headers,"Clasificación Seguimiento Médico $i");
        array_push($headers,"Tiene Seguimiento Médico en el content? $i");
      }

      for ($i=1; $i <= $maxCantLaborMonitoring; $i++) { 
        array_push($headers,"Fecha Seguimiento Laboral $i");
        array_push($headers,"Clasificación Seguimiento Laboral $i");
        array_push($headers,"Tiene Seguimiento Laboral en el content? $i");
        array_push($headers,"Productividad Seguimiento Laboral $i");
      }

      for ($i=1; $i <= $maxCantTracing; $i++) { 
        array_push($headers,"Fecha Seguimiento $i");
        array_push($headers,"Usuario Seguimiento $i");
        array_push($headers,"Descripcion Seguimiento $i");
      }

      \Log::info($headers);

      return $headers;

    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'X' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Z' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AG' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AK' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:CH1',
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
}

