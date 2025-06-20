<?php

namespace App\Exports\IndustrialSecure\Epp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;
use App\Traits\UtilsTrait;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DeliveryExportExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;
    protected $filters;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    public function query()
    {
        $transactions = ElementTransactionEmployee::selectRaw(
          "sau_epp_transactions_employees.*,
          sau_epp_transactions_employees.created_at AS create_delivery,
          sau_epp_elements.*,
          sau_epp_elements.code AS code_element,
          sau_employees.name AS employee,
          sau_employees_positions.name as position,
          sau_epp_elements.name AS element,
          sau_epp_locations.name AS location,          
          count(sau_epp_elements_balance_specific.id) as asignados,
          GROUP_CONCAT(DISTINCT sau_epp_elements_balance_specific.code) AS codes
          "
        )        
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_transactions_employees.location_id')
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        ->groupBy('sau_epp_transactions_employees.id', 'element', 'location', 'sauce.sau_epp_elements.id')
        ->betweenDate($this->filters["dates"]);

      $transactions->company_scope = $this->company_id;

      return $transactions;
    }

    public function map($data): array
    {
      $values = [
        $data->create_delivery,
        $data->employee,
        $data->position,
        $data->location,
        $data->code_element,
        $data->class_element,
        $data->identify_each_element ? 'Identificable' : 'No Identificable',
        $data->element,
        $data->mark,
        $data->asignados,
        $data->identify_each_element ? $data->codes : ''
      ];


      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Fecha',
        'Empleado',
        'Cargo',
        'Ubicación',
        'Código Elemento',
        'Clase',
        'Tipo',
        'Elemento',
        'Marca',
        'Cantidad',
        'Código Específico'
      ];

      return $columns;
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

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Entregas';
    }
}

