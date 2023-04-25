<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class StartIncapacitated implements Rule
{
   /* define if date end in recomendation */
   protected $end_incapacitated;
   protected $has_incapacitated;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($end_incapacitated, $has_incapacitated)
    {
        $this->end_incapacitated = $end_incapacitated;
        $this->has_incapacitated = $has_incapacitated;
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
        if ($this->has_incapacitated == 'SI')
        {
            if ($value != null)
            {
                if ($this->end_incapacitated != null)
                {
                    if (Carbon::createFromFormat('D M d Y', $value)->lte(Carbon::createFromFormat('D M d Y', $this->end_incapacitated)))
                    {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return true;
                }
            } else {
                return true;
            }
        }
        else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->end_incapacitated == null)
            return "Debe diligenciar primero Fecha Fin Incapacidad";

        return ":attribute debe ser una fecha anterior a Fecha Fin Incapacidad";
    }
}
