<?php

namespace App\Exports\PreventiveOccupationalMedicine\Absenteeism;

use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class TableRecordExcel implements FromQuery, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $table;
    protected $company_id;

    public function __construct($table, $company_id)
    {
      $this->company_id = $company_id;

      $this->table = Table::where('id', $table);
      $this->table->company_scope = $this->company_id;
      $this->table = $this->table->first();
    }

    public function query()
    {
      $records = DB::table($this->table->table_name)
      ->selectRaw(implode(", ", $this->table->columns->get('columns')))
      ->orderBy('id');

      return $records;
    }

    public function headings(): array
    {
      return $this->table->columns->get('columns');
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AA1',
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
        return 'Datos';
    }
}

