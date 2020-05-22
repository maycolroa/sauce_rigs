<?php

namespace App\Facades\General;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class Constant extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'constant_service';
    }
}