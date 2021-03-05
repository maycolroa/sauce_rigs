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
        foreach (Notification::get() as $key => $record)
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