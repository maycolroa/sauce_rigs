<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class MusculoskeletalAnalysisTemplateExcel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait, LocationFormTrait;

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
      $result = [];

      foreach ($data as $key => $value)
      {
        array_push($result, $value);
      }
      
      return $result;
    }

    public function headings(): array
    {
      $columns = [];

      $confLocation = $this->getLocationFormConfModule($this->company_id);

      if ($confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($confLocation['process'] == 'SI')
          array_push($columns, $this->keywords['process']);

      if ($confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area']);


      $columns = array_merge($columns, [
            'Identificación Paciente',
            'Nombre',
            'Tipo de Evaluación',
            'Formato Evaluación',
            'Empresa',
            'REGIONAL O PLANTA',
            'Sexo',
            'Edad',
            'Teléfono',
            'Teléfono Alterno',
            $this->keywords['eps'],
            $this->keywords['afp'],
            $this->keywords['position'],
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
        ]);

        return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:CM1',
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

