<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class NewValoration implements Rule
{
   /* define if date end in recomendation */
   protected $has_recommendations;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($has_recommendations)
    {
        $this->has_recommendations = $has_recommendations;
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
        if ($this->has_recommendations == 'SI')
        {
            if ($value != null) 
                return true;
            else 
                return false;
        }
        else if ($this->has_recommendations == 'NO')
            return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ":attribute debe ingresar la Fecha de la nueva valoración";
    }
}
