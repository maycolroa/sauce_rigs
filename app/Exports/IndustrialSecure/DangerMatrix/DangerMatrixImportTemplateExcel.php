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
use App\Traits\LocationFormTrait;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixImportTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;
    use LocationFormTrait;

    protected $data;
    protected $company_id;
    protected $keywords;
    protected $conf;

    public function __construct($data, $company_id)
    {
      $this->data = $data;
      $this->company_id = $company_id;
      $this->keywords = $this->getKeywordQueue($this->company_id);

      $this->conf = QualificationCompany::select('qualification_id');
      $this->conf->company_scope = $this->company_id;
      $this->conf = $this->conf->first();

      if ($this->conf && $this->conf->qualification)
        $this->conf = $this->conf->qualification->name;
      else
        $this->conf = $this->getDefaultCalificationDm();
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
        'Participantes (Separados por “,”)',
        'Actividad',
        'Tipo de actividad (R, NR)',
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
        'Criterios de riesgo - Cumplimiento requisitos legales (SI o NO)',
        'Criterios de riesgo - Alineamiento con las políticas de calidad y de SST (SI o NO)',
        'Criterios de riesgo - Alineamiento con los objetivos y metas (SI o NO)',
        'Medidas de Intervención - Eliminación',
        'Medidas de Intervención – Sustitución (Separados por “,”)',
        'Medidas de Intervención - Controles de ingeniería (Separados por “,”)',
        'Medidas de Intervención - Señalización, Advertencia (Separados por “,”)',
        'Medidas de Intervención - Controles administrativos (Separados por “,”)',
        'Medidas de Intervención – EPP (Separados por “,”)'
      ]);

      if ($this->conf == "Tipo 1")
      {
        $columns = array_merge($columns, [
        'Nivel de Probabilidad',
        'NR Personas',
        'NR Económico',
        'NR Imagen'
        ]);
      }
      else if ($this->conf == "Tipo 2")
      {
        $columns = array_merge($columns, [
        'Frecuencia (RECURRENTE, FRECUENTE, POSIBLE, REMOTO'),
        'Severidad (MENOR, LEVE, GRAVE, CATASTRóFICA)'
        ]);
      }

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

