<?php

namespace App\Models\System\CustomerMonitoring;

use Illuminate\Database\Eloquent\Model;

class NotificationScheduled extends Model
{

    protected $table = 'sau_notification_scheduled';

    protected $fillable = [
        'notification_id',
        'day'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
