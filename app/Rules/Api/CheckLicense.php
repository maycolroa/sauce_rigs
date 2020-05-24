<?php

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;
use DB;
use App\Facades\General\PermissionService;

class CheckLicense implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value)
        {
            $module = PermissionService::getModuleByName('dangerousConditions');
            return PermissionService::existsLicenseByModule($value, $module->id);
        }
        else
            return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "La compañia indicada no tiene licencia activa para esta aplicación";
    }
}
