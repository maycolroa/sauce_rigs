<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use App\Traits\CompanyTrait;
use App\Models\Permission;

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
    protected $appends = ['all_permissions','can'];

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

    public function multiselect()
    {
        return [
          'name' => "{$this->document} - {$this->name}",
          'value' => $this->id
        ];
    }
    
    public function generatePasswordUser()
    {
      return $this->hasMany('App\Models\Administrative\GeneratePasswordUser','sau_generate_password_user');
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
}
