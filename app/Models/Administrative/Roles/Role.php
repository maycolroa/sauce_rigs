<?php

namespace App\Models\Administrative\Roles;

use Laratrust\Models\LaratrustRole;
//use App\Traits\RoleCompanyTrait;
use Illuminate\Support\Facades\Auth;
use Session;

class Role extends LaratrustRole
{
    //use RoleCompanyTrait;

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
	
    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_role_user');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\General\Module', 'module_id');
    }

    /*public function scopeConditionController($query)
    {
        $query->withoutGlobalScopes();

        if (Auth::user()->hasPermission('roles_manage_defined'))
          $query->whereRaw('(sau_roles.company_id = '.Session::get('company_id').' OR sau_roles.company_id IS NULL)');
        else
          $query->where('sau_roles.company_id', Session::get('company_id'));

        return $query;
    }

    public function scopeConditionGeneral($query)
    {
      $query->withoutGlobalScopes();

      $query->where('sau_roles.company_id', Session::get('company_id'));
    }*/

    public function scopeForm($query, $includeSuper = false, $company = null)
    {
      $company = $company ? $company : Session::get('company_id');
      $query->whereRaw("sau_roles.company_id = {$company} OR (sau_roles.company_id IS NULL AND sau_roles.name NOT IN('Contratista', 'Arrendatario'))");

      if (!$includeSuper)
        $query->where('sau_roles.name', '<>', 'Superadmin');

      return $query;
    }

    public function scopeAlls($query, $includeSuper = false, $company = null)
    {
      $company = $company ? $company : Session::get('company_id');
      $query->whereRaw("sau_roles.company_id = {$company} OR sau_roles.company_id IS NULL");

      if (!$includeSuper)
        $query->where('sau_roles.name', '<>', 'Superadmin');

      return $query;
    }

    public function scopeDefined($query)
    {
      return $query->whereNull("sau_roles.company_id");
    }

    public function scopeCompany($query, $company = null)
    {
      $company = $company ? $company : Session::get('company_id');
      return $query->where('sau_roles.company_id', $company);
    }
}
