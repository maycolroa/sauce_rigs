<?php

namespace App\Facades\Check\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class CheckManager extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CheckManager';
    }
}