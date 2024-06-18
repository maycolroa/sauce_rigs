<?php 

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\LegalAspects\Contracts\ProyectContract;

class ProyectContractTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $keywords;

    public function __construct($contract, $company_id)
    {
      $this->company_id = $company_id;
      $this->contract = $contract;
    }

    /**
    * @var eps $eps
    */
    public function map($proyect): array
    {
        return [
            $proyect->id,
            $proyect->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Proyecto',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Proyectos';
    }

    public function query()
    {
      $proyects = ProyectContract::selectRaw("id, name")
            ->join('sau_ct_contracts_proyects', 'sau_ct_proyects.id','sau_ct_contracts_proyects.proyect_id' )
            ->where('sau_ct_contracts_proyects.contract_id', $this->contract->id);

      $proyects->company_scope = $this->company_id;
      
        return $proyects;
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
}