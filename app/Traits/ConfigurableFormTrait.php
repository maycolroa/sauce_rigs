<?php

namespace App\Traits;

use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use Exception;

trait ConfigurableFormTrait
{
    use UtilsTrait;

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
                $data = ConfigurationsCompany::company($company_id)->findByKey($key);
            else
                $data = ConfigurationsCompany::findByKey($key);

            return $data;

        } catch( Exception $e) {
            return 'default';
        }
    }

    protected function getSelectOptions($key, $company_id = NULL)
    {
        try
        {     
            if ($company_id)
                $data = ConfigurationsCompany::company($company_id)->findByKey($key);
            else
                $data = ConfigurationsCompany::findByKey($key);

            return $this->multiSelectFormat($data);

        } catch( Exception $e) {
            return [];
        }
    }
}