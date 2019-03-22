<?php

namespace App\Facades\ConfigurationsCompany\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class ConfigurationsCompany extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ConfigurationsCompany';
    }
}