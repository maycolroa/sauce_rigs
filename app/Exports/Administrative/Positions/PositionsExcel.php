<?php

namespace App\Exports\Administrative\Positions;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Administrative\Users\User;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\Administrative\Positions\EmployeePosition;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class PositionsExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $elements;
    protected $elements_id = [];

    public function __construct($company_id)
    {
      $this->company_id = $company_id;

      $this->elements = Element::selectRaw("id, name");

      $this->elements->company_scope = $this->company_id;
      $this->elements = $this->elements->get();

      foreach ($this->elements as $key => $value) {
          array_push($this->elements_id, $value->id);
      }
    }

    public function query()
    {
      $team = $this->company_id;

      $positions = EmployeePosition::select('*');

      $positions->company_scope = $this->company_id;

      return $positions;
    }

    public function map($data): array
    {
      $values = [$data->name];

      $element_position_id = [];

      if ($data->elements->count() > 0)
      {
        foreach ($data->elements as $key2 => $value2) 
        {
          array_push($element_position_id, $value2->id);
        }

        foreach ($this->elements_id as $key => $value) 
        {
          if (in_array($value, $element_position_id))
                array_push($values, 'SI');
            else
                array_push($values, 'NO');
        }
      }
      else
      {
        foreach ($this->elements as $key2 => $value2) 
        {
            array_push($values, 'NO');
        }
      }

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Cargo'
      ];

      $element = [];

      foreach ($this->elements as $key => $value) {
        array_push($element, $value->name);
      }

      $columns = array_merge($columns, $element);

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:R1',
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
      return 'Cargos';
    }
}


