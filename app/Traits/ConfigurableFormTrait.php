<?php

namespace App\Traits;

use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Configuration;
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
            
        } catch (Exception $e) {
            return 'default';
        }
    }

    protected function getSelectOptions($key, $multiselect = true, $company_id = NULL)
    {
        try
        {     
            if ($company_id)
                $data = ConfigurationsCompany::company($company_id)->findByKey($key);
            else
                $data = ConfigurationsCompany::findByKey($key);

            return $multiselect ? $this->multiSelectFormat($data) : $data;

        } catch (Exception $e) {
            return $this->getDefaultSelectOptions($key, $multiselect);
        }
    }

    private function getDefaultSelectOptions($key, $multiselect)
    {
        try
        {     
            $data = Configuration::getConfiguration($key);

            return $multiselect ? $this->multiSelectFormat($data) : $data;

        } catch (Exception $e) {
            return [];
        }
    }
}