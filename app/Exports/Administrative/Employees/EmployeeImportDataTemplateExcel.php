<?php

namespace App\Exports\Administrative\Employees;

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

class EmployeeImportDataTemplateExcel implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $data;
    protected $formModel;
    protected $company_id;
    protected $keywords;

    public function __construct($data, $formModel, $company_id)
    {
      $this->data = $data;
      $this->formModel = $formModel;
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
        'Identificación',
        'Nombre',
        'Fecha Nacimiento (YYYY-MM-DD)',
        'Sexo (Masculino, Femenino, Sin Sexo)',
        'Email',
        'Fecha de Ingreso (YYYY-MM-DD)',
        $this->keywords['regional'],
        $this->keywords['headquarter'],
        $this->keywords['process'],
        $this->keywords['area'],
        $this->keywords['position'],
        $this->keywords['businesses']
      ];

      if ($this->formModel == 'default')
      {
        return array_merge($columns, [
          'Negocio',
          "{$this->keywords['eps']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['eps']}', se debe ingresar el codigo de la {$this->keywords['eps']})"
        ]);
      }
      else if ($this->formModel == 'vivaAir' || $this->formModel == 'manpower')
      {
        return array_merge($columns, [
          "{$this->keywords['eps']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['eps']}', se debe ingresar el codigo de la {$this->keywords['eps']})",
          "{$this->keywords['afp']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['afp']}', se debe ingresar el codigo de la {$this->keywords['afp']})"
        ]);
      }
      else if ($this->formModel == 'misionEmpresarial')
      {
        return array_merge($columns, [
          "{$this->keywords['eps']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['eps']}', se debe ingresar el codigo de la {$this->keywords['eps']})",
          "{$this->keywords['afp']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['afp']}', se debe ingresar el codigo de la {$this->keywords['afp']})",
          "{$this->keywords['arl']} (Los posibles valores se encuentran en la pestaña '{$this->keywords['arl']}', se debe ingresar el codigo de la {$this->keywords['arl']})",
          'Número de contratos',
          'Fecha de último contrato',
          'Tipo de contrato (Termino fijo, Termino indefinido, Obra labor, Prestación de servicios)'
        ]);
      }
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:R1',
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
        return 'Registros';
    }
}

