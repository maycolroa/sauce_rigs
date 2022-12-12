<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogUserActivitySystem extends Model
{    
    protected $table = 'sau_log_activities_users_system';

    protected $fillable = ['user_id', 'company_id', 'module', 'description'];
}
