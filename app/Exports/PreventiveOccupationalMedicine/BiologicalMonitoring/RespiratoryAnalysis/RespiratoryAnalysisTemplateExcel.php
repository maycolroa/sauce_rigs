<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class RespiratoryAnalysisTemplateExcel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
        return 'Analisis Respiratorio';
    }
}

