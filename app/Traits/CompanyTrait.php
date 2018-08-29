<?php 

namespace App\Traits;

use App\Scopes\CompanyScope;

trait CompanyTrait {

    /**
     * Boot the Active Events trait for a model.
     *
     * @return void
     */
    public static function bootCompanyTrait()
    {
        static::addGlobalScope(new CompanyScope);
    }

}