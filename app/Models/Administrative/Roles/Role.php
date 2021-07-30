<?php

namespace App\Models\Administrative\Roles;

use Laratrust\Models\LaratrustRole;
//use App\Traits\RoleCompanyTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\System\Licenses\License;
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

    public function scopeForm($query, $includeSuper = false, $company = null)
    {
      $company = $company ? $company : Session::get('company_id');

      $modules = License::
              join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')]);

      $modules->company_scope = $company;
      $modules = $modules->pluck('sau_license_module.module_id')->unique();

      if ($modules->count() > 0)
        $modules = $modules->implode(",");
      else
        $modules = 'null';
      
      $query->whereRaw("(sau_roles.company_id = {$company} OR (sau_roles.company_id IS NULL AND sau_roles.name NOT IN('Contratista', 'Arrendatario') AND module_id IN ({$modules})))");

      if (!$includeSuper)
        $query->where('sau_roles.name', '<>', 'Superadmin');

      return $query;
    }

    public function scopeAlls($query, $includeSuper = false, $company = null)
    {
      $company = $company ? $company : Session::get('company_id');

      $modules = License::
              join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')]);

      $modules->company_scope = $company;
      $modules = $modules->pluck('sau_license_module.module_id')->unique()->implode(",");

      $query->whereRaw("(sau_roles.company_id = {$company} OR (sau_roles.company_id IS NULL AND module_id IN ({$modules})))");

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
