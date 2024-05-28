<?php 

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\LegalAspects\Contracts\ProyectContract;

class ProyectTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $keywords;

    public function __construct($company_id)
    {
      $this->company_id = $company_id;
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
      $proyects = ProyectContract::selectRaw("id, name")->where('company_id', $this->company_id);
      
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