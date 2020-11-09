<?php 

namespace App\Exports\LegalAspects\LegalMatrix\Laws;

use App\Models\LegalAspects\LegalMatrix\Entity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\UtilsTrait;

class EntityTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
    * @var eps $eps
    */
    public function map($entity): array
    {
        return [
            $entity->name
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Entes';
    }

    public function query()
    {
        $entities = Entity::select('name')
        ->where('company_id', $this->company_id);

        return $entities;    
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