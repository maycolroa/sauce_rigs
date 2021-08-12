<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use App\Traits\UtilsTrait;
use App\Traits\LocationFormTrait;


Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class RespiratoryAnalysisExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait, LocationFormTrait;

    protected $company_id;
    protected $filters;
    protected $keywords;
    protected $confLocation;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->confLocation = $this->getLocationFormConfModule($this->company_id);
    }

    public function query()
    {
      $data = RespiratoryAnalysis::select(
        'sau_bm_respiratory_analysis.*',
        'sau_employees_regionals.name AS regional',
        'sau_employees_headquarters.name AS headquarter',
        'sau_employees_processes.name AS process',
        'sau_employees_areas.name AS area')
      ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_bm_respiratory_analysis.employee_regional_id')
      ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_bm_respiratory_analysis.employee_headquarter_id')
      ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_bm_respiratory_analysis.employee_process_id')
      ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_bm_respiratory_analysis.employee_area_id')
      ->inRegional($this->filters['regional'], $this->filters['filtersType']['regional']);

      $data->company_scope = $this->company_id;

      return $data;
    }

    public function map($data): array
    {
      $values = [];

      if ($this->confLocation['regional'] == 'SI')
          array_push($values, $data->regional);

      if ($this->confLocation['headquarter'] == 'SI')
          array_push($values, $data->headquarter);

      if ($this->confLocation['process'] == 'SI')
      {
          array_push($values, $data->process);
          array_push($values, $data->macroprocess);
      }

      if ($this->confLocation['area'] == 'SI')
          array_push($values, $data->area);

      $values = array_merge($values, [
        $data->patient_identification,
        $data->name,
        $data->sex,
        $data->deal,
        $data->regional,
        $data->date_of_birth,
        $data->age,
        $data->income_date,
        $data->antiquity,
        $data->area,
        $data->position,
        $data->habits,
        $data->history_of_respiratory_pathologies,
        $data->measurement_date,
        $data->mg_m3_concentration,
        $data->ir,
        $data->type_of_exam,
        $data->year_of_spirometry,
        $data->spirometry,
        $data->date_of_realization,
        $data->symptomatology,
        $data->cvf_average_percentage,
        $data->vef1_average_percentage,
        $data->vef1_cvf_average,
        $data->fef_25_75_porcentage,
        $data->interpretation,
        $data->type_of_exam_2,
        $data->date_of_realization_2,
        $data->rx_oit,
        $data->quality,
        $data->yes_1,
        $data->not_1,
        $data->answer_yes_describe,
        $data->yes_2,
        $data->not_2,
        $data->answer_yes_describe_2,
        $data->other_abnormalities,
        $data->fully_negative,
        $data->observation,        
        $data->breathing_problems,
        $data->classification_according_to_ats,
        $data->ats_obstruction_classification,
        $data->ats_restrictive_classification,
        $data->state
      ]);

      return $values;
    }

    public function headings(): array
    {
      $columns = [];

      if ($this->confLocation['regional'] == 'SI')
          array_push($columns, $this->keywords['regional']);

      if ($this->confLocation['headquarter'] == 'SI')
          array_push($columns, $this->keywords['headquarter']);

      if ($this->confLocation['process'] == 'SI')
      {
          array_push($columns, $this->keywords['process']);
          array_push($columns, 'Macroproceso');
      }

      if ($this->confLocation['area'] == 'SI')
          array_push($columns, $this->keywords['area']);

      $columns = array_merge($columns, [
            'Cedula',
            'Nombres',
            'sexo',
            'NEGOCIO',
            'PLANTA',
            'Fecha nacimiento',
            'Edad',
            'Fecha ingreso a la empresa',
            'Antiguedad',
            'Area',
            'Cargo actual  ',
            'Habitos',
            'Antecedentes de patologias respiratorias',
            'fecha de realizacion de mediciones',
            'Concentración mg m3',
            'IR',
            'Tipo de examen',
            'AÑO DE ESPIROMETRIA',
            'ESPIROMETRIA',
            'Fecha realización',
            'Sintomatología',
            'CVF % promedio',
            'VEF 1% promedio',
            'VEF1 / CVF % promedio',
            'FEF 25-75%',
            'Interpretación',
            'Tipo de examen2',
            'Fecha de realizacion',
            'RX OIT',
            'CALIDAD',
            'SI1',
            'NO1',
            'RESPUESTA SI, DESCRIBIR',
            'SI2',
            'NO2',
            'RESPUESTA SI DESCRIBIR',
            'OTRAS ANORMALIDADES',
            'TOTALMENTE NEGATIVA',
            'Observacion',
            'Problema Respiratorio',
            'CLASIFICACION SEGÚN ATS',
            'CLASIFICACION OBSTRUCCION ATS',
            'CLASIFICACION RESTRICTIVO ATS',
            'Estado'
        ]);

        return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AR1',
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
        return 'Analisis Respiratorio';
    }
}

