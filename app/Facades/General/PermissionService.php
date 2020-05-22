<?php

namespace App\Facades\General;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class PermissionService extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'permission_service';
    }
}