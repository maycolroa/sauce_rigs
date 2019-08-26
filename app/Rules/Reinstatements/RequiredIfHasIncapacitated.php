<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class RequiredIfHasIncapacitated implements Rule
{

    /**
     * defines if check has incapacitated
     * @var string
     */
    protected $has_incapacitated;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($has_incapacitated)
    {
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
        if ($this->has_incapacitated == 'SI') {
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
        $hasIncapacitatedLabel = trans('validation.attributes.has_incapacitated');
        return $message = "El campo :attribute es obligatorio cuando {$hasIncapacitatedLabel} es SI.";
    }
}
