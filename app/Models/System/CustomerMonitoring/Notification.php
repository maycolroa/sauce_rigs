<?php

namespace App\Models\System\CustomerMonitoring;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'sau_notifications';

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User','sau_notification_user');
    }

    public function days()
    {
        return $this->hasMany(NotificationScheduled::class, 'notification_id');

    }
}
