<?php

namespace App\Exports\Administrative\Users;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExcel implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize 
{
  protected $users;
  public function __construct(Collection $users)
    {
      $this->users = $users;
      
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      return $this->users;
    }

    public function map($users): array
    {
        return [
          $users->name,
          $users->email,
          $users->state,
          $users->document,
          $users->document_type,
          $users->role,
          $users->created_at,
          $users->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
          'Nombre',
          'Email',
          'Estado',
          'Documento',
          'Tipo de Documento',
          'Rol',
          'Fecha Creación',
          'Fecha Actualización',
        ];
    }
}
