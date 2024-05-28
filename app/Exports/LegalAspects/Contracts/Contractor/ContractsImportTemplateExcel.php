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
    protected $proyect;

    public function __construct($data, $proyect)
    {
      $this->data = $data;
      $this->proyect = $proyect;
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
          'Tipo de empresa (*) (Contratista, Arrendatario o Proveedor)',
          'Clasificación (Unidad de Produccion Agropecuaria, Empresa)',
          'Nombre de la empresa (*)',
          'Nit (*)',
          'Razón social (*)',
          '¿La empresa realiza tareas de alto riesgo? (SI, NO)(*)',
          'Tareas de riesgos (Trabajo en alturas, Energias peligrosas, Trabajos en caliente, Espacios confinados) (Separados por “,”si son varios)',
          'Actividades (Tomar el código de la actividad a asignar al contratista de la pestaña Actividades, de ser varias actividades debe separar los códigos por coma (,))',
          'Proyectos (Tomar el código del proyecto a asignar al contratista de la pestaña Proyectos, de ser varios proyectos debe separar los códigos por coma (,) Opcional)',
          'Responsable de la contratista (Tomar el código del usuario a asignar al contratista de la pestaña Usuarios, de ser varios usuarios debe separar los códigos por coma (,) Opcional)',
          'Dirección',
          'Teléfono',
          'Nombre del representante legal',
          'Nombre del responsable del SG-SST',
          'Nombre del encargado de gestión ambiental',
          'Actividad económica de la empresa',
          'Arl',
          'Número de trabajadores',
          'Clase de riesgo (Clase de riesgo I, Clase de riesgo II, Clase de riesgo III, Clase de riesgo IV, Clase de riesgo V)',
        ];

      return $columns;

    }

    public static function afterSheet(AfterSheet $event)
    {
      $red = "d9534f";
      $blue = "5Bc0de";
      $yellow = "f4d75e";      
      $white = "FFFFFF";

      $event->sheet->styleCells(
        'A1:Z1',
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

      $event->sheet->styleCells(
        'A1:C1',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $yellow
                ],
            ]
          ]
      );

      $event->sheet->styleCells(
        'D1:M1',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $red
                ],
            ]
          ]
      );

      $event->sheet->styleCells(
        'N1:V1',
          [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $blue
                ],
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

