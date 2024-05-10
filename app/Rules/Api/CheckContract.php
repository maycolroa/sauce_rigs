<?php

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;
use DB;

class CheckContract implements Rule
{
    /**
     * defines if check has indefinite recommendations
     * @var string
     */
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
            return DB::table('sau_ct_information_contract_lessee')->where('nit', $value)->where('active', DB::raw("'SI'"))->where('company_id', 1)->exists();
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
        return "El contratista no existe o esta desactivado";
    }
}
