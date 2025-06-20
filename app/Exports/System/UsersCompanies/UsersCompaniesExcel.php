<?php

namespace App\Exports\System\UsersCompanies;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class UsersCompaniesExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $filters;

    public function __construct($filters)
    {
      $this->filters = $filters;
    }

    public function query()
    {
      $role = Role::defined()->where('name', 'Superadmin')->first();

      $usersCompanies = User::select(
          'sau_users.id',
          'sau_users.name',
          'sau_users.email', 
          'sau_users.active',
          'sau_companies.name as company',
          'sau_roles.name AS role',
          'sau_modules.display_name AS module'
      )
      ->withoutGlobalScopes()
      ->join('sau_company_user', 'sau_users.id', 'sau_company_user.user_id')
      ->join('sau_companies', 'sau_companies.id', 'sau_company_user.company_id')
      ->leftJoin('sau_role_user', function($q){ 
              $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                ->on('sau_role_user.team_id', '=', 'sau_companies.id');
      })
      ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
      ->leftJoin('sau_modules', 'sau_modules.id', 'sau_roles.module_id')
      ->leftJoin('sau_permission_role', 'sau_permission_role.role_id', 'sau_roles.id')
      ->leftJoin('sau_permissions', 'sau_permissions.id', 'sau_permission_role.permission_id')
      ->whereRaw("(sau_role_user.role_id <> {$role->id} OR sau_role_user.role_id IS NULL)")
      ->where('sau_users.active', DB::raw("'SI'"))
      ->groupBy('sau_users.id', 'company', 'sau_roles.id', 'sau_modules.id');


      if (isset($this->filters['permissions']) && $this->filters['filtersType']['permissions'] && COUNT($this->filters['permissions']) > 0)
        $usersCompanies->inPermissions($this->filters['permissions'], $this->filters['filtersType']['permissions']);

      if (isset($this->filters['modules']) && $this->filters['filtersType']['modules'] && COUNT($this->filters['modules']) > 0)
        $usersCompanies->inModules($this->filters['modules'], $this->filters['filtersType']['modules']);

      if (isset($this->filters['companies']) && $this->filters['filtersType']['companies'] && COUNT($this->filters['companies']) > 0)
        $usersCompanies->inCompanies($this->filters['companies'], $this->filters['filtersType']['companies']);

      return $usersCompanies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->email,
        $data->active,
        $data->module,
        $data->role,
        $data->company
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Nombre',
        'Email',
        '¿Activo?',
        'Modulo',
        'Rol',
        'Compañia'
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


