<?php

namespace App\Facades\System;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class NotificationManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notification_manager';
    }
}