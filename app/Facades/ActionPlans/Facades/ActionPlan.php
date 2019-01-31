<?php

namespace App\Facades\ActionPlans\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class ActionPlan extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ActionPlan';
    }
}