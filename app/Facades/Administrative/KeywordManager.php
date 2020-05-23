<?php

namespace App\Facades\Administrative;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class KeywordManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'keyword_manager';
    }
}