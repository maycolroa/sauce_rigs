<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class AudiometryImportDataTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $data;

    public function __construct($data)
    {
      $this->data = $data;
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
        return [
          'Identificacion',
          'Nombre',
          'Sexo (M, F)',
          'Email',
          'Fecha Nacimiento',
          'Cargo',
          'Centro de Costo',
          'Regional',
          'Sede',
          'Área',
          'Proceso',
          'EPS (Los posibles valores se encuentran en la pestaña “EPS”, se debe ingresar el codigo de la EPS)',
          'Fecha Ingreso Empresa',
          'Fecha',
          'Eventos Previos',
          'EPP (Copa, Inserción, Moldeable)',
          'Nivel Exposición – Disometría (85 a 95 dB, No realizada, Menos de 80 dB, 80 a 84.9 dB)',
          'AD 500',
          'AD 1000',
          'AD 2000',
          'AD 3000',
          'AD 4000',
          'AD 6000',
          'AD 8000',
          'AI 500',
          'AI 1000',
          'AI 2000',
          'AI 3000',
          'AI 4000',
          'AI 6000',
          'AI 8000',
          'OD 500',
          'OD 1000',
          'OD 2000',
          'OD 3000',
          'OD 4000',
          'OI 500',
          'OI 1000',
          'OI 2000',
          'OI 3000',
          'OI 4000',
          'Recomendaciones Generales',
          'Observaciones Generales'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'R' => NumberFormat::FORMAT_NUMBER,
            'S' => NumberFormat::FORMAT_NUMBER,
            'T' => NumberFormat::FORMAT_NUMBER,
            'U' => NumberFormat::FORMAT_NUMBER,
            'V' => NumberFormat::FORMAT_NUMBER,
            'W' => NumberFormat::FORMAT_NUMBER,
            'X' => NumberFormat::FORMAT_NUMBER,
            'Y' => NumberFormat::FORMAT_NUMBER,
            'Z' => NumberFormat::FORMAT_NUMBER,
            'AA' => NumberFormat::FORMAT_NUMBER,
            'AB' => NumberFormat::FORMAT_NUMBER,
            'AC' => NumberFormat::FORMAT_NUMBER,
            'AD' => NumberFormat::FORMAT_NUMBER,
            'AE' => NumberFormat::FORMAT_NUMBER,
            'AF' => NumberFormat::FORMAT_NUMBER,
            'AG' => NumberFormat::FORMAT_NUMBER,
            'AH' => NumberFormat::FORMAT_NUMBER,
            'AI' => NumberFormat::FORMAT_NUMBER,
            'AJ' => NumberFormat::FORMAT_NUMBER,
            'AK' => NumberFormat::FORMAT_NUMBER,
            'AL' => NumberFormat::FORMAT_NUMBER,
            'AM' => NumberFormat::FORMAT_NUMBER,
            'AN' => NumberFormat::FORMAT_NUMBER,
            'AO' => NumberFormat::FORMAT_NUMBER
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $red = "d9534f";
      $blue = "5Bc0de";
      $yellow = "f4d75e";
      $white = "FFFFFF";

      $event->sheet->styleCells(
        'A1:AQ1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Arial',
            ]
          ]
      );

      $event->sheet->styleCells(
        'A1:Q1',
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
        'R1:X1',
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
        'Y1:AE1',
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

      $event->sheet->styleCells(
        'AF1:AJ1',
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
        'AK1:AO1',
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

      $event->sheet->styleCells(
        'AP1:AQ1',
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
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Registros';
    }
}

