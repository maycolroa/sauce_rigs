<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;
use App\Traits\CompanyTrait;

class Role extends LaratrustRole
{
    use CompanyTrait;

    protected $fillable = [
        'name', 'description'
    ];

    public function multiselect(){
      return [
        'name' => $this->name,
        'value' => $this->id
      ];
    }
}
