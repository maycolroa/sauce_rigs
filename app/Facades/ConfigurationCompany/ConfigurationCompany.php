<?php

namespace App\Facades\ConfigurationsCompany;

use Illuminate\Support\Facades\App;
use App\Models\Administrative\Configurations\ConfigurationCompany;
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

    /**
     * Key attribute
     *
     * @var String
     */
    private $key;

    /**
     * Value attribute
     *
     * @var String
     */
    private $value;

    /**
     * The possible keys and their descriptions
     *
     * @var Array
     */
    private $keys;

    public function __construct()
    {
        $this->company = Session::get('company_id') ? Session::get('company_id') : null;

        $this->keys = [
            'location_level_form' => 'Nivel localización en formulario',
            'days_alert_expiration_date_action_plan' => 'Días de alerta por fecha de vencimiento cercana para los planes de acción',
            'days_alert_expiration_date_contract_file_upload' => 'Días de alerta por fecha de vencimiento cercana para los archivos cargados en el módulo de contratistas'
        ];
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
     * Set the key
     *
     * @param String $key
     * @return $this
     */
    public function key($key)
    {
        if (!is_string($key))
            throw new \Exception('Invalid key format');

        $this->key = $key;

        return $this;
    }

    /**
     * Set the value
     *
     * @param String $value
     * @return $this
     */
    public function value($value)
    {
        if (empty($value))
            throw new \Exception('Invalid value format');

        $this->value = $value;

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

    /**
     * Returns an array with all configurations and their values, if any
     *
     * @return Array
     */
    public function findAll()
    {
        $data = [];

        foreach ($this->keys as $key => $value) 
        {
            try
            {
                $value = $this->findByKey($key);
            } catch(Exception $e){
                $value = '';
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Save the company configuration according to the defined key
     * 
     */
    public function save()
    {
        if (empty($this->key))
            throw new \Exception('A valid key has not been entered.');

        if (empty($this->value))
            throw new \Exception('A valid value has not been entered.');

        $configuration = ConfigurationCompany::where('key', $this->key);
        $configuration->company_scope = $this->company;
        $configuration = $configuration->first();

        if (!$configuration)
        {
            ConfigurationCompany::create([
                'company_id' => $this->company,
                'key' => strtolower($this->key),
                'value' => $this->value,
                'observation' => isset($this->keys[$this->key]) ? $this->keys[$this->key] : ''
            ]);
        }
        else
        {
            $configuration->update([
                'value' => $this->value,
                'observation' => isset($this->keys[$this->key]) ? $this->keys[$this->key] : ''
            ]);
        }

        $this->key = null;
        $this->value = null;
    }
}