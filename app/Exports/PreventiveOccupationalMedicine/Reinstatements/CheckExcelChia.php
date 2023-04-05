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
use App\Traits\LocationFormTrait;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class CheckExcelChia implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait, LocationFormTrait;

    protected $company_id;
    protected $data;
    protected $keywords;

    public function __construct($company_id, $data)
    {
      \Log::info('chia');
      $this->company_id = $company_id;
      $this->data = $data;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);
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

      $position = $business = $regional = $restriction = $headquarter = $process = $area = '';

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

      if ($this->confLocation['headquarter'] == 'SI')
      {
          $headquarter = EmployeeHeadquarter::query();
          $headquarter->company_scope = $this->company_id;
          $headquarter = $headquarter->find($employee->employee_headquarter_id);
          $headquarter = $headquarter ? $headquarter->name : '';
      }

      if ($this->confLocation['process'] == 'SI')
      {
          $process = EmployeeProcess::query();
          $process->company_scope = $this->company_id;
          $process = $process->find($employee->employee_process_id);
          $process = $process ? $process->name : '';
      }

      if ($this->confLocation['area'] == 'SI')
      {
          $area = EmployeeArea::query();
          $area->company_scope = $this->company_id;
          $area = $area->find($employee->employee_area_id);
          $area = $area ? $area->name : '';
      }

      $values = [
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
        $regional
      ];

      if ($this->confLocation['headquarter'] == 'SI')
            array_push($values, $headquarter);

      if ($this->confLocation['process'] == 'SI')
      {
          array_push($values, $process);
      }

      if ($this->confLocation['area'] == 'SI')
          array_push($values, $area);

      $values = array_merge($values, [ 
        ($employee->eps ? $employee->eps->name : ''),
        $data->disease_origin,
        $data->cie10Code->code,
        $data->cie10Code->description,
        $data->cie10Code->system,
        $data->cie10Code->category,
        $data->laterality,
        $data->disease_origin_2,
        $data->cie10_code_2_id ? $data->cie10Code2->code : NULL,
        $data->cie10_code_2_id ? $data->cie10Code2->description : NULL,
        $data->cie10_code_2_id ? $data->cie10Code2->system : NULL,
        $data->cie10_code_2_id ? $data->cie10Code2->category : NULL,
        $data->laterality_2,
        $data->disease_origin_3,
        $data->cie10_code_3_id ? $data->cie10Code3->code : NULL,
        $data->cie10_code_3_id ? $data->cie10Code3->description : NULL,
        $data->cie10_code_3_id ? $data->cie10Code3->system : NULL,
        $data->cie10_code_3_id ? $data->cie10Code3->category : NULL,
        $data->laterality_3,
        $data->disease_origin_4,
        $data->cie10_code_4_id ? $data->cie10Code4->code : NULL,
        $data->cie10_code_4_id ? $data->cie10Code4->description : NULL,
        $data->cie10_code_4_id ? $data->cie10Code4->system : NULL,
        $data->cie10_code_4_id ? $data->cie10Code4->category : NULL,
        $data->laterality_4,
        $data->disease_origin_5,
        $data->cie10_code_5_id ? $data->cie10Code5->code : NULL,
        $data->cie10_code_5_id ? $data->cie10Code5->description : NULL,
        $data->cie10_code_5_id ? $data->cie10Code5->system : NULL,
        $data->cie10_code_5_id ? $data->cie10Code5->category : NULL,
        $data->laterality_5,
        $data->qualification_dme,
        $data->has_recommendations,
        $data->start_recommendations,
        $data->indefinite_recommendations,
        $data->end_recommendations,
        $data->relocated,
        $data->monitoring_recommendations,
        $data->origin_recommendations,
        $data->detail,
        $data->has_restrictions,
        $restriction,
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
      ]);

      return $values;
    }

    public function headings(): array
    {
      $columns = [
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
        $this->keywords['businesses']
      ];

      if ($this->confLocation['regional'] == 'SI')
            array_push($columns, $this->keywords['regional']);

      if ($this->confLocation['headquarter'] == 'SI')
          array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocation['process'] == 'SI')
          array_push($columns, $this->keywords['process']);

      if ($this->confLocation['area'] == 'SI')
          array_push($columns, $this->keywords['area']);

      $columns = array_merge($columns, [
        $this->keywords['eps'],
        $this->keywords['disease_origin'],
        'Código CIE10',
        'Descripción CIE10',
        'Sistema',
        'Categoría',
        'Lateralidad',
        $this->keywords['disease_origin'].'(2)',
        'Código CIE10 (2)',
        'Descripción CIE10 (2)',
        'Sistema (2)',
        'Categoría (2)',
        'Lateralidad (2)',
        $this->keywords['disease_origin'].'(3)',
        'Código CIE10 (3)',
        'Descripción CIE10 (3)',
        'Sistema (3)',
        'Categoría (3)',
        'Lateralidad (3)',
        $this->keywords['disease_origin'].'(4)',
        'Código CIE10 (4)',
        'Descripción CIE10 (4)',
        'Sistema (4)',
        'Categoría (4)',
        'Lateralidad (4)',
        $this->keywords['disease_origin'].'(5)',
        'Código CIE10 (5)',
        'Descripción CIE10 (5)',
        'Sistema (5)',
        'Categoría (5)',
        'Lateralidad (5)',
        'Calificación DME',
        '¿Tiene recomendaciones?',
        'Fecha Inicio Recomendaciones',
        '¿Recomendacions indefinidas?',
        'Fecha Fin Recomendaciones',
        '¿Reubidado?',
        'Fecha de seguimiento a recomendaciones',
        'Procedencia de las recomendaciones',
        $this->keywords['detail_recommendations'],
        '¿Tiene Restricción?',
        'Parte del cuerpo afectada',
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
      ]);

      return $columns;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            //'F' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AH' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AL' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AX' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            //'AK' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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

