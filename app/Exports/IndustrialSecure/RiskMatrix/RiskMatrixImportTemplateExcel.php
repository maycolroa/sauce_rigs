<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

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
use App\Traits\LocationFormTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class RiskMatrixImportTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;
    use LocationFormTrait;

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
      $columns = [];

      $confLocation = $this->getLocationFormConfModule($this->company_id);

      if ($confLocation['regional'] == 'SI')
        array_push($columns, $this->keywords['regional']);

      if ($confLocation['headquarter'] == 'SI')
        array_push($columns, $this->keywords['headquarter']);

      if ($confLocation['process'] == 'SI')
      {
          array_push($columns, $this->keywords['process']);
          array_push($columns, 'Macroproceso');
      }

      if ($confLocation['area'] == 'SI')
        array_push($columns, $this->keywords['area']);

      $columns = array_merge($columns, [
        'Secuencia Riesgo (Debe repetirse la cantidad de causas que ingrese)',
        'Nomenclatura',
        'Participantes (Separados por “,”)',
        'Sub-Proceso',
        'Evento de Riesgo',
        'Categoría del Riesgo',
        'Causa',
        'Controles Actuales (Separados por "-")',
        'Económico (Valores validos 0-5)',
        'Cal. en la atención y seg. del paciente (Valores validos 0-5)',
        'Reputacional (Valores validos 0-5)',
        'Legal Regulatorio (Valores validos 0-5)',
        'Ambiental (Valores validos 0-5)',
        'Max. Impacto Inherente (Valores validos 0-5)',
        'Descripción Impacto inherente',
        'Max. Frecuencia Inherente (Valores validos 0-5)',
        'Descripción Frecuencia Inherente',
        'Exposición Inherente',
        'El control apunta a disminuir (Frecuencia, Impacto, Ambos)',
        'Naturaleza (Automático, Manual, Mixto)',
        'Evidencia (SI, NO)',
        'Cobertura (Inmaterial, Parcial, Total)',
        'Documentación del control (Documentado, No Documentado, Parcialmente Documentado)',
        'Segregación (SI, NO)',
        'Evaluacion del control',
        '% de Mitigación (80, 60, 45, 20, 0)',
        'Max. Impacto Residual (Valores validos 0-5)',
        'Descripción Impacto Residual',
        'Max. Frecuencia Residual (Valores validos 0-5)',
        'Descripción Frecuencia Residual',
        'Exposición Residual',
        'Maximo Impacto Evento Riesgo',
        'Indicadores (Separados por “-”)'
      ]);

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

