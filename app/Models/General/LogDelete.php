<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogDelete extends Model
{    
    protected $table = 'sau_log_delete';

    protected $fillable = ['user_id', 'company_id', 'module', 'description'];

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_log_delete.created_at', $dates);
            return $query;
        }
    }
}
