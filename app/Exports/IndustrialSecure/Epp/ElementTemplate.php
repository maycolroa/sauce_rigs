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
use App\Models\IndustrialSecure\Epp\Element;

class ElementTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $keywords;
    protected $tipo;

    public function __construct($company_id, $tipo)
    {
      $this->company_id = $company_id;
      $this->tipo = $tipo;
    }

    /**
    * @var eps $eps
    */
    public function map($element): array
    {
        return [
            $element->code,
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
      $elements = Element::selectRaw("code, name")->where('identify_each_element', $this->tipo);

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