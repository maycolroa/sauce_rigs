<?php

namespace App\Facades\StorageFiles\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class StorageFile extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'StorageFile';
    }
}