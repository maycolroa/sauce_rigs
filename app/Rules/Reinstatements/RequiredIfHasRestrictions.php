<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class RequiredIfHasRestrictions implements Rule
{

    /**
     * defines if check has restrictions or not
     * only possible values must be "SI" or "NO"
     * @var string
     */
    protected $has_restrictions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($has_restrictions)
    {
        $this->has_restrictions = $has_restrictions;
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
        if ($this->has_restrictions == 'SI') {
            return $value ? true : false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Si el reporte tiene restricción, este campo es requerido';
    }
}
