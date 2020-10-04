<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogUserActivity extends Model
{    
    protected $table = 'sau_log_user_activity';

    protected $fillable = ['user_id', 'company_id', 'description'];
}
