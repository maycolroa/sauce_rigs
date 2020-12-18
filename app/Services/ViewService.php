<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Facades\General\PermissionService;
use App\Facades\Administrative\KeywordManager;
use App\Models\Administrative\Users\User;
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
}