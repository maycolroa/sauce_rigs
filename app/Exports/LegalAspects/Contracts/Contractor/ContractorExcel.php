<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ContractorExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;

    public function __construct($company_id, $filters)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
    }

    public function query()
    {
        $contracts = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.*',
                'sau_ct_list_check_resumen.total_standard AS total_standard',
                'sau_ct_list_check_resumen.total_c AS total_c',
                'sau_ct_list_check_resumen.total_nc AS total_nc',
                'sau_ct_list_check_resumen.total_sc AS total_sc',
                'sau_ct_list_check_resumen.total_p_c AS total_p_c',
                'sau_ct_list_check_resumen.total_p_nc AS total_p_nc'
            )
            ->leftJoin('sau_ct_list_check_resumen', 'sau_ct_list_check_resumen.contract_id', 'sau_ct_information_contract_lessee.id');

        if (COUNT($this->filters) > 0)
        {
            $contracts->rangePercentageCumple($this->filters["rangePC"]);
        }

        $contracts->company_scope = $this->company_id;

        return $contracts;
    }

    public function map($data): array
    {
      return [
        $data->nit,
        $data->social_reason,
        $data->type,
        $data->high_risk_work,
        $data->active,
        $data->total_standard,
        $data->total_c,
        $data->total_nc,
        $data->total_sc,
        ($data->total_p_c / 100),
        ($data->total_p_nc / 100)
      ];
    }

    public function headings(): array
    {
        return [
          'Nit',
          'Razón social',
          'Tipo',
          '¿Alto riesgo?',
          '¿Activo?',
          'Estándares',
          '#Cumple',
          '#No Cumple',
          '#Sin Calificar',
          '%Cumple',
          '%No Cumple'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,
            'K' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
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
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Contratistas';
    }
}

