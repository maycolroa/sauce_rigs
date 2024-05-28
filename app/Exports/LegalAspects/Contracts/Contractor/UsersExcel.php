<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Administrative\Users\User;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class UsersExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;

    public function __construct($company_id)
    {
      $this->company_id = $company_id;
    }

    public function query()
    {
      $team = $this->company_id;

      $users = User::select(
        'sau_users.id AS id',
        'sau_users.name AS name'
      )
      ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
      ->leftJoin('sau_role_user', function($q) use ($team) { 
          $q->on('sau_role_user.user_id', '=', 'sau_users.id')
            ->on('sau_role_user.team_id', '=', DB::raw($team));
      })
      ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
      ->where('sau_roles.id', '<>', 5)
      ->groupBy('sau_users.id');

      $users->company_scope = $this->company_id;

      return $users;
    }

    public function map($data): array
    {
      $values = [
        $data->id,
        $data->name,
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Codigo',
        'Nombre'
      ];

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
      return 'Usuarios';
    }
}


