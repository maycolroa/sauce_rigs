<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

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

class ContractsImportTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
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
        'Nombre (*)',
        'Documento de identificación (*)',
        'Email (*)',
        'Tipo de empresa (*) (Contratista o Arrendatario)',
        'Clasificación (UPA, Empresa)',
        'Nombre de la empresa (*)',
        'Nit (*)',
        'Razón social (*)',
        '¿La empresa realiza tareas de alto riesgo? (*)',
        'Tareas de riesgos (Trabajo en alturas, Energias peligrosas, Trabajos en caliente, Espacios confinados) (Separados por “,”si son varios)',
        'Dirección',
        'Teléfono',
        'Nombre del representante legal',
        'Nombre del responsable del SG-SST',
        'Nombre del encargado de gestión ambiental',
        'Actividad económica de la empresa',
        'Arl',
        'Número de trabajadores',
        'Clase de riesgo (Clase de riesgo I, Clase de riesgo II, Clase de riesgo III, Clase de riesgo IV, Clase de riesgo V)'

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
        return 'Contratistas';
    }
}

