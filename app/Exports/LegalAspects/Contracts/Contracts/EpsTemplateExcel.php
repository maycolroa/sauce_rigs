<?php 

namespace App\Exports\LegalAspects\Contracts\Contracts;

use App\Models\Administrative\Employees\EmployeeEPS;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\UtilsTrait;

class EpsTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $keywords;

    public function __construct($company_id)
    {
      $this->keywords = $this->getKeywordQueue($company_id);
    }

    /**
    * @var eps $eps
    */
    public function map($eps): array
    {
        return [
            $eps->code,
            $eps->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return $this->keywords['eps'];
    }

    public function query()
    {
        return EmployeeEPS::query();
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
}