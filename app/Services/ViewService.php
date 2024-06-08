<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Facades\General\PermissionService;
use App\Facades\Administrative\KeywordManager;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Session;

class ViewService
{
    /**
     * Devuelve una coleccion con los roles definidos
     * indicando si el usuario lo tiene o no
     *
     * @return collect
     */
    public function getHasRole()
    {
        return PermissionService::getHasRole(Auth::user(), Session::get('company_id'));
    }

    /**
     * Devuelve una coleccion con los roles definidos
     * indicando si el usuario lo tiene o no
     *
     * @return collect
     */
    public function getCan()
    {
        return PermissionService::getCan(Auth::user(), Session::get('company_id'));
    }

    /**
     * Devuelve una coleccion con los permisos
     * indicando si el usuario lo tiene o no
     * 
     * @return collect
     */
    public function getKeywords()
    {
        return KeywordManager::getKeywords(Session::get('company_id'));
    }

    public function getTerms()
    {
        return Auth::user()->terms_conditions;
    }

    public function getInventaryEpp()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'inventory_management');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getFilterInspection()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'filter_inspections');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getProyectContract()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'contracts_use_proyect');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getIntegrationContract()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'company_there_integration_contract');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function allAuthData()
    {
        return [
            'can' => $this->getCan(), 
            'hasRole' => $this->getHasRole(), 
            'inventaryEpp' => $this->getInventaryEpp(), 
            'proyectContract' => $this->getProyectContract(), 
            'integrationContract' => $this->getIntegrationContract(), 
            'terms' => $this->getTerms(),
            'user_auth' => Auth::user(),
            'company_id' => Session::get('company_id'),
            'filter_inspections' => $this->getFilterInspection()
        ];
    }
}