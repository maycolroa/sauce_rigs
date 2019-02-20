<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;
use App\Traits\CompanyTrait;

class Role extends LaratrustRole
{
    use CompanyTrait;

    protected $table = 'sau_roles';

    protected $fillable = [
        'name', 'description', 'module_id'
    ];

    public function multiselect(){
      return [
        'name' => $this->name,
        'value' => $this->id
      ];
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
