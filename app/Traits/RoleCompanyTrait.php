<?php 

namespace App\Traits;

use App\Scopes\RoleCompanyScope;

trait RoleCompanyTrait {

    /**
     * Boot the Active Events trait for a model.
     *
     * @return void
     */
    public static function bootRoleCompanyTrait()
    {
        static::addGlobalScope(new RoleCompanyScope);
    }

}