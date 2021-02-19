<?php

namespace App\Exports\LegalAspects\Contracts\ListCheck;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ListCheckContractExcel implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $contract;

    public function __construct($contract)
    {
      $this->contract = $contract;
    }

    public function collection()
    {
        $sql = SectionCategoryItems::select(
            'sau_ct_section_category_items.*',
            'sau_ct_standard_classification.standard_name as standard_name'
        )
        ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
        ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');

        $items = [];

        if ($this->contract->classification == 'UPA')
        {
            if ($this->contract->number_workers <= 10)
            {
                if ($this->contract->risk_class == "Clase de riesgo I" || $this->contract->risk_class == "Clase de riesgo II" || $this->contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '3 estandares')->get();
                }
                else if ($this->contract->risk_class == "Clase de riesgo IV" || $this->contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
        }
        else if ($this->contract->classification == 'Empresa')
        {
            if ($this->contract->number_workers <= 10)
            {
                if ($this->contract->risk_class == "Clase de riesgo I" || $this->contract->risk_class == "Clase de riesgo II" || $this->contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '7 estandares')->get();
                }
            }
            else if ($this->contract->number_workers > 10 && $this->contract->number_workers <= 50)
            {
                if ($this->contract->risk_class == "Clase de riesgo I" || $this->contract->risk_class == "Clase de riesgo II" || $this->contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '21 estandares')->get();
                }
                else if ($this->contract->risk_class == "Clase de riesgo IV" || $this->contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
            else if ($this->contract->number_workers > 50)
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
        }

        $qualifications = Qualifications::pluck("description", "id");

        //Obtiene los items calificados
        $items_calificated = ItemQualificationContractDetail::
                  where('contract_id', $this->contract->id)
                ->pluck("qualification_id", "item_id");

        $totales = [
            'c' => 0,
            'nc' => 0,
            'sc' => 0,
            't' => 0
        ];

        $contract = $this->contract;

        $items->transform(function($item, $index) use ($qualifications, $items_calificated, $contract, &$totales) {

            $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';

            if ($item->qualification == 'Cumple' || $item->qualification == 'No Aplica')
                $totales['c']++;
            else if ($item->qualification == 'No Cumple')
                $totales['nc']++;
            else if ($item->qualification == '')
            {
                $totales['nc']++;
                $totales['sc']++;
            }

            $totales['t']++;

            #\Log::info($totales);

            return $item;
        });

        #\Log::info($totales);

        if ($items->count() > 0)
        {
            $add = new \stdClass();
            $add->item_name = '';
            $add->qualification = '';
            $items->push($add);

            $add = new \stdClass();
            $add->item_name = 'TOTALES ('.$items[0]->standard_name.'):';
            $add->qualification = 'Total: '.$totales['t'].' ';
            $items->push($add);

            $add = new \stdClass();
            $add->item_name = '';
            $add->qualification = 'Cumple: '.$totales['c'].' ('.round(($totales['c'] / $totales['t']) * 100, 1).'%)';
            $items->push($add);

            $add = new \stdClass();
            $add->item_name = '';
            $add->qualification = 'No Cumple: '.$totales['nc'].' ('.round(($totales['nc'] / $totales['t']) * 100, 1).'%)';
            $items->push($add);

            $add = new \stdClass();
            $add->item_name = '';
            $add->qualification = '#Items No calificados: '.$totales['sc'];
            $items->push($add);
        }

        return $items;
    }

    public function map($data): array
    {
      return [
        $data->item_name,
        $data->qualification
      ];
    }

    public function headings(): array
    {
        return [
          'Item',
          'Calificación'
        ];
    }

    public function columnFormats(): array
    {
        return [
          /*'D' => NumberFormat::FORMAT_NUMBER,
          'E' => NumberFormat::FORMAT_NUMBER,
          'F' => NumberFormat::FORMAT_PERCENTAGE_00,
          'G' => NumberFormat::FORMAT_PERCENTAGE_00,*/
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:C1',
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
        return 'Nit - '.$this->contract->nit;
    }
}

