<?php

namespace App\Models\Administrative\Users;

use Illuminate\Database\Eloquent\Model;

class LogUserModify extends Model
{    
    protected $table = 'sau_log_user_modify';

    protected $fillable = ['modifier_user', 'modified_user', 'modification', 'roles_old', 'roles_new'];
}
