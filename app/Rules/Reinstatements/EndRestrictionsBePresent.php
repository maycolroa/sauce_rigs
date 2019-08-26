<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class EndRestrictionsBePresent implements Rule
{

    /**
     * defines if check has restrictions
     * @var string
     */
    protected $has_restrictions;

    /**
     * defines if check has indefinite restrictions
     * @var string
     */
    protected $indefinite_restrictions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($has_restrictions, $indefinite_restrictions)
    {
        $this->has_restrictions = $has_restrictions;
        $this->indefinite_restrictions = $indefinite_restrictions;
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
        if ($this->has_restrictions == 'SI' && $this->indefinite_restrictions == 'NO') {
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
        $hasrestrictionsLabel = trans('validation.attributes.has_restrictions');
        $indefiniterestrictionsLabel = trans('validation.attributes.indefinite_restrictions');
        return $message = "El campo :attribute es obligatorio cuando {$hasrestrictionsLabel} es SI y {$indefiniterestrictionsLabel} es NO.";
    }
}
