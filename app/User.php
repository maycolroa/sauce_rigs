<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use App\Traits\CompanyTrait;
use App\Models\Permission;
use App\Models\Role;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    use CompanyTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'sau_users';

    protected $fillable = [
        'name', 'email', 'password','state', 'document', 'document_type', 'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = ['all_permissions','can','hasRole'];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_roles';

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
      	return $this->belongsToMany('App\Administrative\Company','sau_company_user');
    }

    public function generatePasswordUser(){
          return $this->hasMany('App\Models\Administrative\GeneratePasswordUser','sau_generate_password_user');
    }

    public function actionPlanResponsibles()
    {
        return $this->hasMany('App\Models\ActionPlansActivity', 'responsible_id');
    }

    public function actionPlanCreator()
    {
        return $this->hasMany('App\Models\ActionPlansActivity', 'user_id');
    }

    public function multiselect()
    {
        return [
          'name' => "{$this->document} - {$this->name}",
          'value' => $this->id
        ];
    }

    public function contractInformation(){
      	return $this->belongsToMany('App\Models\LegalAspects\UserContractLesseeDetail','sau_user_information_contract_lessee', 'user_id', 'information_id');
    }

    public function contractInfo()
    {
      	return $this->belongsToMany('App\LegalAspects\ContractLessee','sau_user_information_contract_lessee', 'user_id', 'information_id');
    }
    
    public function roleUser(){
      	return $this->belongsToMany('App\Models\Role','sau_role_user');
	}

	public function itemsCalificatedContract(){
      	return $this->belongsToMany('App\Models\LegalAspects\ItemQualificationContractDetail','sau_ct_item_qualification_contract');
    }
    

    /**
     * Get all user permissions.
     *
     * @return bool
     */
    public function getAllPermissionsAttribute()
    {
        return $this->allPermissions();
    }
    
     /**
     * Get all user permissions in a flat array.
     *
     * @return array
     */
    public function getCanAttribute()
    {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if ($this->can($permission->name)) {
                $permissions[$permission->name] = true;
            } else {
                $permissions[$permission->name] = false;
            }
        }
        return $permissions;
    }

    public function getHasRoleAttribute()
    {
        $roles = [];

        foreach (Role::withoutGlobalScopes()->whereNull('sau_roles.company_id')->get() as $role)
        {
            $roles[$role->name] = false;
        }
        
        foreach ($this->roleUser as $role)
        {
            if (!$role->company_id && isset($roles[$role->name]))
                $roles[$role->name] = true;
        }

        return $roles;
    }

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
}
