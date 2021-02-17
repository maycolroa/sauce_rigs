<?php

namespace App\Models\System\CustomerMonitoring;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'sau_notifications';

    protected $fillable = [
        'name'
    ];
}
