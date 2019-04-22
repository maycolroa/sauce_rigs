<?php

namespace App\Models\Administrative\Users;

use Illuminate\Database\Eloquent\Model;

class GeneratePasswordUser extends Model
{
    protected $table = 'sau_generate_password_user';
    const UPDATED_AT = null;
    
    public function users(){
        return $this->belongsToMany(User::class, 'sau_users');
    }
}
