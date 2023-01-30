<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogUserActivitySystem extends Model
{    
    protected $table = 'sau_log_activities_users_system';

    protected $fillable = ['user_id', 'company_id', 'module', 'description'];

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_log_activities_users_system.created_at', $dates);
            return $query;
        }
    }
}
