<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

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
use App\Models\Administrative\Configurations\ConfigurationCompany;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ContractsEmployeesTemplate implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $data;
    protected $company_id;
    protected $contract;
    protected $proyect;

    public function __construct($data, $company_id, $contract, $proyect)
    {
      $this->data = $data;
      $this->company_id = $company_id;
      $this->contract = $contract;
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
        'Identificación (*)',
        'Fecha Nacimiento (YYYY-MM-DD) (*)',
        'Sexo (Masculino, Femenino, Sin Sexo)*',
        'Teléfono de residencia (*)',
        'Teléfono movil (*)',
        'Estado civil (*)(Soltero, Casado)',
        'Dirección (*)',
        'Email (*)',
        'Jornada laboral (Tomar valor de la pestaña Jornadas)',
        'Departamento (Tomar el códigoD de la pestaña Departamentos y municipios) (*)',
        'Municipio (Tomar el códigoM de la pestaña Departamentos y municipios) (*)',
        'Fechas de ingreso',
        'Cargo (*)',
        'Condición de discapacidad (SI, NO) (*)',
        'Descripción Condicion de discapacidad (Solo si Condición de discapacidad es SI)',
        'Contacto de emergencia (*)',
        'Teléfono Contacto de emergencia (*)',
        'Tipo de sangre (Tomar el tipo de la pestaña Tipos de sangre) (*)',
        'Salario (*)',
        'AFP (Tomar el código de la pestaña AFP) (*)',
        'EPS (Tomar el código de la pestaña EPS) (*)',
        'Actividades (Tomar el código de la actividad a asignar al empleado de la pestaña Actividades, de ser varias actividades debe separar los códigos por coma (,))'
      ];

      if ($this->proyect == 'SI')
      {
        $columns = array_merge($columns, [
          'Proyectos (Tomar el código del proyecto a asignar al empleado de la pestaña Proyectos, de ser varios proyectos debe separar los códigos por coma (,))'
        ]);
      }

      return $columns;

    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
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
        return 'Contratistas - Empleados';
    }
}