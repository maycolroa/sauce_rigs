<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogDelete extends Model
{    
    protected $table = 'sau_log_delete';

    protected $fillable = ['user_id', 'company_id', 'module', 'description'];
}
