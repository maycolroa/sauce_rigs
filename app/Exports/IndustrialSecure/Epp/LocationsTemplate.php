<?php 

namespace App\Exports\IndustrialSecure\Epp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\IndustrialSecure\Epp\Location;

class LocationsTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct($company_id)
    {
      $this->company_id = $company_id;
    }

    /**
    * @var eps $eps
    */
    public function map($location): array
    {
        return [
            $location->id,
            $location->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Ubicacion',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Ubicaciones';
    }

    public function query()
    {
      $locations = Location::selectRaw("id, name");

      $locations->company_scope = $this->company_id;
      
        return $locations;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:B1',
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