<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class MusculoskeletalAnalysisExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
      $data = MusculoskeletalAnalysis::select('sau_bm_musculoskeletal_analysis.*')
      ->inConsolidatedPersonalRiskCriterion($this->filters['consolidatedPersonalRiskCriterion'], $this->filters['filtersType']['consolidatedPersonalRiskCriterion'])
      ->inBranchOffice($this->filters['branchOffice'], $this->filters['filtersType']['branchOffice'])
      ->inCompanies($this->filters['companies'], $this->filters['filtersType']['companies'])
      ->betweenDate($this->filters["dates"]);;

      $data->company_scope = $this->company_id;

      return $data;
    }

    public function map($data): array
    {
      return [
        $data->patient_identification,
        $data->name,
        $data->date,
        $data->evaluation_type,
        $data->evaluation_format,
        $data->company,
        $data->branch_office,
        $data->sex,
        $data->age,
        $data->phone,
        $data->phone_alternative,
        $data->eps,
        $data->afp,
        $data->position,
        $data->antiquity,
        $data->state,
        $data->ant_atep_ep,
        $data->which_ant_atep_ep,
        $data->exercise_habit,
        $data->exercise_frequency,
        $data->liquor_habit,
        $data->liquor_frequency,
        $data->exbebedor_habit,
        $data->liquor_suspension_time,
        $data->cigarette_habit,
        $data->cigarette_frequency,
        $data->habit_extra_smoker,
        $data->cigarrillo_suspension_time,
        $data->activity_extra_labor,
        $data->weight,
        $data->size,
        $data->imc,
        $data->imc_lassification,
        $data->abdominal_perimeter,
        $data->abdominal_perimeter_classification,
        $data->diagnostic_code_1,
        $data->diagnostic_1,
        $data->diagnostic_code_2,
        $data->diagnostic_2,
        $data->diagnostic_code_3,
        $data->diagnostic_3,
        $data->diagnostic_code_4,
        $data->diagnostic_4,
        $data->diagnostic_code_5,
        $data->diagnostic_5,
        $data->diagnostic_code_6,
        $data->diagnostic_6,
        $data->diagnostic_code_7,
        $data->diagnostic_7,
        $data->diagnostic_code_8,
        $data->diagnostic_8,
        $data->diagnostic_code_9,
        $data->diagnostic_9,
        $data->diagnostic_code_10,
        $data->diagnostic_10,
        $data->diagnostic_code_11,
        $data->diagnostic_11,
        $data->diagnostic_code_12,
        $data->diagnostic_12,
        $data->diagnostic_code_13,
        $data->diagnostic_13,
        $data->cardiovascular_risk,
        $data->osteomuscular_classification,
        $data->osteomuscular_group,
        $data->age_risk,
        $data->pathological_background_risks,
        $data->extra_labor_activities_risk,
        $data->sedentary_risk,
        $data->imc_risk,
        $data->consolidated_personal_risk_punctuation,
        $data->consolidated_personal_risk_criterion,
        $data->prioritization_medical_criteria,
        $data->concept,
        $data->recommendations,
        $data->observations,
        $data->restrictions,
        $data->remission,
        $data->description_medical_exam,
        $data->symptom,
        $data->symptom_type,
        $data->body_part,
        $data->periodicity,
        $data->workday,
        $data->symptomatology_observations,
        $data->id3
      ];
    }

    public function headings(): array
    {
        return [
            'Identificación Paciente',
            'Nombre',
            'Fecha',
            'Tipo de Evaluación',
            'Formato Evaluación',
            'Empresa',
            'REGIONAL O PLANTA',
            'Sexo',
            'Edad',
            'Teléfono',
            'Teléfono Alterno',
            'EPS',
            'AFP',
            'Cargo',
            'Antigüedad',
            'ESTADO',
            'Ant. ATEP-EP ',
            'Cuales Ant. ATEP-EP ',
            'Habito Ejercicio',
            'Frecuencia Ejercicio',
            'Habito Licor',
            'Frecuencia Licor',
            'Habito Exbebedor',
            'Tiempo Suspensión Licor',
            'Habito Cigarrillo',
            'Frecuencia Cigarrillo',
            'Habito Exfumador',
            'Tiempo Suspensión Cigarrillo',
            'Actividad Extralaboral',
            'Peso',
            'Talla',
            'IMC',
            'Clasificación IMC',
            'Perímetro Abdominal',
            'Clasificación Perímetro Abdominal',
            'Código Diagnóstico 1',
            'Diagnóstico 1',
            'Código Diagnóstico 2',
            'Diagnóstico 2',
            'Código Diagnóstico 3',
            'Diagnóstico 3',
            'Código Diagnóstico 4',
            'Diagnóstico 4',
            'Código Diagnóstico 5',
            'Diagnóstico 5',
            'Código Diagnóstico 6',
            'Diagnóstico 6',
            'Código Diagnóstico 7',
            'Diagnóstico 7',
            'Código Diagnóstico 8',
            'Diagnóstico 8',
            'Código Diagnóstico 9',
            'Diagnóstico 9',
            'Código Diagnóstico 10',
            'Diagnóstico 10',
            'Código Diagnóstico 11',
            'Diagnóstico 11',
            'Código Diagnóstico 12',
            'Diagnóstico 12',
            'Código Diagnóstico 13',
            'Diagnóstico 13',
            'Riesgo Cardiovascular',
            'Clasificación Osteomuscular',
            'Grupo Osteomuscular',
            'Riesgo Edad (A)',
            'Riesgos Antecedentes Patológicos (B)',
            'Riesgo Actividades Extralaborales (C )',
            'Riesgo Sedentarismo (D)',
            'Riesgo IMC (E )',
            'Consolidado Riesgo Personal (Puntuación)',
            'Consolidado Riesgo Personal (Criterio)',
            'Criterio Médico de Priorización',
            'Concepto',
            'Recomendaciones',
            'Observaciones',
            'Restricciones',
            'Remisión',
            'Descripción Examen Médico',
            'Síntomas',
            'Tipo Sintoma',
            'Parde del cuerpo',
            'Periodicidad',
            'Jornada Laboral',
            'Observaciones2',
            'ID3'            
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:CI1',
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
        return 'Analisis Osteomuscular';
    }
}

