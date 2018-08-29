<?php

namespace App\Vuetable\Facades;

use Illuminate\Support\Facades\Facade;

class Vuetable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vuetable';
    }
}
