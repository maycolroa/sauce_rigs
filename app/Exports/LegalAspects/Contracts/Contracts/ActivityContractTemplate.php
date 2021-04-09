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
use App\Models\LegalAspects\Contracts\ActivityContract;

class ActivityContractTemplate implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
    public function map($activity): array
    {
        return [
            $activity->id,
            $activity->name
        ];
    }

    public function headings(): array
    {
        return [
            'Código',
            'Actividad',
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Actividades';
    }

    public function query()
    {
      $activities = ActivityContract::selectRaw("id, name")
            ->join('sau_ct_contracts_activities', 'sau_ct_activities.id','sau_ct_contracts_activities.activity_id' )
            ->where('sau_ct_contracts_activities.contract_id', $this->contract->id);

      $activities->company_scope = $this->company_id;
      
        return $activities;
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