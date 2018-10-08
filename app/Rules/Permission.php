<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Permission implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        foreach($value as $v)
        {
            $datos = json_decode($v);
            
            if (isset($datos->permissions) && COUNT($datos->permissions) > 0)
                return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El campo :attribute es obligatorio';
    }
}
