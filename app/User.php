<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use App\Traits\CompanyTrait;

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
}
