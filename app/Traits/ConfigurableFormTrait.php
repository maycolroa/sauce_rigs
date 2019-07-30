<?php

namespace App\Traits;

use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use Exception;

trait ConfigurableFormTrait
{
    /**
     * returns the configuration
     *
     * @return Array
     */
    protected function getFormModel($key, $company_id = NULL)
    {   
        try
        {     
            if ($company_id)
                $locationLevelForm = ConfigurationsCompany::company($company_id)->findByKey($key);
            else
                $locationLevelForm = ConfigurationsCompany::findByKey($key);

            return $locationLevelForm;

        } catch( Exception $e) {
            return 'default';
        }
    }
}