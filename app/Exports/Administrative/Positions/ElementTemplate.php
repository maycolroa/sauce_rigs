<?php 

namespace App\Exports\Administrative\Positions;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\IndustrialSecure\Epp\Element;

class ElementTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
    public function map($element): array
    {
        return [
            $element->id,
            $element->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Elemento',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Elementos';
    }

    public function query()
    {
      $elements = Element::selectRaw("id, name");

      $elements->company_scope = $this->company_id;
      
        return $elements;
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