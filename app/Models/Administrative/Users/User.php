<?php

namespace App\Models\Administrative\Users;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use App\Traits\CompanyTrait;
use App\Traits\PermissionTrait;
use App\Traits\LocationFormTrait;
use App\Models\General\Team;
use App\Models\General\Permission;
use App\Models\General\Keyword;
use App\Models\Administrative\Roles\Role;
use Constant;
use Session;
use DB;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    use CompanyTrait;
    use PermissionTrait;
    use LocationFormTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'sau_users';

    protected $fillable = [
        'name', 'email', 'password','active', 'state', 'document', 'document_type', 'default_module_url', 'module_id', 'last_login_at', 'created_at', 'updated_at', 'medical_record', 'sst_license', 'terms_conditions', 'firm'
    ];

    /**
     * The accessors to append to the model's array form.
    *
    * @var array
    */
    //protected $appends = [/*'all_permissions','can','hasRole','keywords'*/];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_company_user';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $attributes = [
        'state' => 0
    ];

    public function setPasswordAttribute($value)
    {
        if(isset($value))
        {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function companies(){
      	return $this->belongsToMany('App\Models\General\Company','sau_company_user')->where('active', 'SI');
    }

    public function contractResponsibles(){
        return $this->belongsToMany('App\Models\LegalAspects\Contracts\ContractLesseeInformation','sau_ct_contract_responsibles', 'user_id');
    }

    public function generatePasswordUser(){
          return $this->hasMany('App\Models\Administrative\Users\GeneratePasswordUser','sau_generate_password_user');
    }

    public function actionPlanResponsibles()
    {
        return $this->hasMany('App\Models\Administrative\ActionPlans\ActionPlansActivity', 'responsible_id');
    }

    public function actionPlanCreator()
    {
        return $this->hasMany('App\Models\Administrative\ActionPlans\ActionPlansActivity', 'user_id');
    }

    public function multiselect()
    {
        return [
          'name' => "{$this->document} - {$this->name}",
          'value' => $this->id
        ];
    }

    public function multiselectActionPlan()
    {
        return [
          'name' => "{$this->name}",
          'value' => $this->id
        ];
    }

    public function defaultModule()
    {
        return $this->belongsTo('App\Models\General\Module', 'module_id');
    }
    
    public function roleUser(){
      	return $this->belongsToMany('App\Models\Administrative\Roles\Role','sau_role_user');
	}
    

    /**
     * Get all user permissions.
     *
     * @return bool
     */
    /*public function getAllPermissionsAttribute()
    {
        return $this->allPermissions();
    }*/
    
     /**
     * Get all user permissions in a flat array.
     *
     * @return array
     */
    public function getCan()
    {
        $modules = $this->getModulePermissions();
        $permission_enabled = [];

        foreach ($modules as $key => $value)
        {
            $permission_enabled = array_merge($permission_enabled,  array_values($value));
        }

        $permissions = [];
        
        foreach (Permission::all() as $permission) {
            if (in_array($permission->name, $permission_enabled)) {
                $permissions[$permission->name] = true;
            } else {
                $permissions[$permission->name] = false;
            }
        }

        return $permissions;
    }

    public function getHasRole()
    {
        $team = Team::where('name', Session::get('company_id'))->first();

        $roles = [];

        foreach (Role::defined()->get() as $role)
        {
            $roles[$role->name] = $this->hasRole($role->name, $team);
        }

        return $roles;
    }

    public function multiselectRoles($team)
    {
        $roles = collect([]);

        foreach (Role::alls(true)->get() as $role)
        {
            if ($this->hasRole($role->name, $team))
                $roles->push([
                    'name' => $role->name,
                    'value' => $role->id
                ]);
        }

        return $roles;
    }

    /*public function checkRoleDefined($role)
    {
        $roles = $this->getHasRoleAttribute();

        return $roles[$role];
    }*/

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }

    public function scopeActive($query, $active = true)
    {
        if ($active)
            $query->where('sau_users.active', 'SI');
        else
            $query->where('sau_users.active', 'NO');

        return $query;
    }

    public function getKeywords($company = null)
    {
        $company = $company ? $company : Session::get('company_id');

        $keywords = DB::table(DB::raw(
                        "(SELECT
                            k.name AS name,
                            IF (c.display_name IS NULL, k.display_name, c.display_name) AS display_name
                        FROM
                            sau_keywords k
                        LEFT JOIN sau_keyword_company c ON c.keyword_id = k.id AND 
                            (
                                c.company_id = $company OR c.company_id IS NULL
                            )) AS t"
                        )
                    )
                    ->pluck('display_name', 'name');

        return $keywords;
    }

    public function headquarters()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_reinc_user_headquarter')->withPivot('company_id');
    }

    public function systemsApply()
    {
        return $this->belongsToMany('App\Models\LegalAspects\LegalMatrix\SystemApply', 'sau_lm_user_system_apply')->withPivot('company_id');
    }

    public function getLocationForm($company = null)
    {
        $company = $company ? $company : Session::get('company_id');
        $conf = $this->getLocationFormConfModule($company);

        return $conf;
    }

    public function isSuperAdmin($team)
    {
        return $this->hasRole(Constant::getConstant('ROLE_SUPER'), $team);
    }

    public function scopeInRoles($query, $roles, $typeSearch = 'IN')
    {
        if (COUNT($roles) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_role_user.role_id', $roles);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_role_user.role_id', $roles);
        }

        return $query;
    }

    public function scopeInPermissions($query, $permissions, $typeSearch = 'IN')
    {
        if (COUNT($permissions) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_permission_role.permission_id', $permissions);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_permission_role.permission_id', $permissions);
        }

        return $query;
    }

    public function headquartersFilter()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_ph_user_headquarters')->withPivot('company_id');
    }

    public function regionals()
    {
        return $this->belongsToMany('App\Models\Administrative\Regionals\EmployeeRegional', 'sau_ph_user_regionals')->withPivot('company_id');
    }

    public function processes()
    {
        return $this->belongsToMany('App\Models\Administrative\Processes\EmployeeProcess', 'sau_ph_user_processes')->withPivot('company_id');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Administrative\Areas\EmployeeArea', 'sau_ph_user_areas')->withPivot('company_id');
    }
}
