<?php

namespace App\Facades\RiskMatrix;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\AppService
 */
class RiskMatrixHistoryManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'risk_matrix_history_manager';
    }
}