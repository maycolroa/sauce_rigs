<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

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
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixImportTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $data;
    protected $company_id;
    protected $keywords;

    public function __construct($data, $company_id)
    {
      $this->data = $data;
      $this->company_id = $company_id;
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
      $columns = [
        $this->keywords['regional'],
        $this->keywords['headquarter'],
        $this->keywords['process'],
        $this->keywords['area'],
        'Participantes (Separados por “,”)',
        'Actividad',
        'Tipo de actividad',
        'Peligro (Biológico, Químico, etc.)',
        'Descripción del peligro (Separados por “,”)',
        'Peligro Generado (Sitio de trabajo, Vecindad, Fuera del sitio de trabajo) (Separados por “,”si son varios)',
        'Posibles consecuencias del peligro (Separados por “,”)',
        'Fuente generadora',
        'Expuestos - Colaboradores',
        'Expuestos - Contratistas',
        'Expuestos - Visitantes',
        'Expuestos - Estudiantes',
        'Expuestos - Arrendatarios',
        'Controles Existentes - Controles de ingeniería (Separados por “,”)',
        'Controles Existentes – Sustitución (Separados por “,”)',
        'Controles Existentes - Señalización, Advertencia (Separados por “,”)',
        'Controles Existentes - Controles administrativos (Separados por “,”) ',
        'Controles Existentes – EPP (Separados por “,”)',
        'Criterios de riesgo - Cumplimiento requisitos legales',
        'Criterios de riesgo - Alineamiento con las políticas de calidad y de SST',
        'Criterios de riesgo - Alineamiento con los objetivos y metas',
        'Criterios de riesgo - Aceptabilidad del riesgo',
        'Medidas de Intervención - Eliminación',
        'Medidas de Intervención – Sustitución (Separados por “,”)',
        'Medidas de Intervención - Controles de ingeniería (Separados por “,”)',
        'Medidas de Intervención - Señalización, Advertencia (Separados por “,”)',
        'Medidas de Intervención - Controles administrativos (Separados por “,”)',
        'Medidas de Intervención – EPP (Separados por “,”)',
        'Nivel de Probabilidad',
        'NR Personas',
        'NR Económico',
        'NR Imagen',
        'Plan de acción - Descripción',
        'Responsable',
        'Fecha de vencimiento',
        'Fecha de ejecución',
        'Estado',
        'Observación'
      ];

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AP1',
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
        return 'Mátriz de peligro';
    }
}

