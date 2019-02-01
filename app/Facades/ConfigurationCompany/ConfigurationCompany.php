<?php

namespace App\Facades\ConfigurationsCompany;

use Illuminate\Support\Facades\App;
use App\Models\ConfigurationCompany;
use Exception;
use Session;

class ConfigurationsCompany
{
    /**
     * Id of the company
     *
     * @var Integer
     */
    private $company;

    public function __construct()
    {
        $this->company = Session::get('company_id') ? Session::get('company_id') : null;
    }

    /**
     * Set the id of the company
     *
     * @param Integer $company
     * @return $this
     */
    public function company($company)
    {
        if (!is_numeric($company))
            throw new \Exception('Invalid company format');

        $this->company = $company;

        return $this;
    }

    /**
     * Find the configuration by its key and return its value
     *
     * @param String $key
     * @return Array
     * @return String
     */
    public function findByKey($key)
    {
        if (!$key)
            throw new \Exception('key required');

        if (empty($this->company))
            throw new \Exception('A valid company was not entered');

        $configuration = ConfigurationCompany::select('value')->where('key', $key);
        $configuration->company_scope = $this->company;
        $configuration = $configuration->first();
    
        if (!$configuration)
            throw new Exception('Parameter not found in configuration company table');

        $value = json_decode($configuration->value);

        if(gettype($value) == "object" || gettype($value) == 'array')
            return $value;

        return $configuration->value;
    }

    /**
     * Find the configuration by its key and return its value for all the companies
     *
     * @param String $key
     * @return Array
     */
    public function findAllCompanyByKey($key)
    {
        if (!$key)
            throw new \Exception('key required');

        $configurations = ConfigurationCompany::select('company_id', 'value')->where('key', $key)->withoutGlobalScopes()->get();

        if ($configurations->count() == 0)
            throw new Exception('Parameter not found in configuration company table');
        
        $data = [];

        foreach ($configurations as $keyItem => $valueItem)
        {
            $value = json_decode($valueItem->value);

            if(gettype($value) == "object" || gettype($value) == 'array')
                $configurations[$keyItem]->value = $value;

            array_push($data, [
                "company_id" => $valueItem->company_id,
                "value" => $valueItem->value
            ]);
        }

        return $data;
    }
}