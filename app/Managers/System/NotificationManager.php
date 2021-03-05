<?php

namespace App\Managers\System;

use DB;
use Exception;
use App\Managers\BaseManager;
use App\Models\System\CustomerMonitoring\NotificationScheduled;
use App\Models\System\CustomerMonitoring\Notification;
use Carbon\Carbon;
use ReflectionClass;

class NotificationManager extends BaseManager
{
    CONST ALERTS = [
        'customer_monitoring' => 'CustomerMonitoring'
    ];

    public function sendNotification()
    {
        $this->now = Carbon::now();

        $records = Notification::select('sau_notifications.*', 'sau_notification_scheduled.day as day')
        ->join('sau_notification_scheduled', 'sau_notification_scheduled.notification_id', 'sau_notifications.id')
        ->where('sau_notification_scheduled.day', $this->now->day)
        ->get();

        foreach ($records as $key => $record)
        {
            if (isset($this::ALERTS[$record->code]) && $this::ALERTS[$record->code])
            {
                $class = "App\Managers\System\Alerts\\{$this::ALERTS[$record->code]}";
                $item = (new ReflectionClass($class))->newInstanceArgs([$record]);
                $item->send();
            }
        }
    }
}