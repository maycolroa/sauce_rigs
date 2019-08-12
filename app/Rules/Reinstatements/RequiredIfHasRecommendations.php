<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class RequiredIfHasRecommendations implements Rule
{

    /**
     * defines if check has recommendations or not
     * only possible values must be "SI" or "NO"
     * @var string
     */
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
        if ($this->has_recommendations == 'SI') {
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
        return 'Si el reporte tiene recomendaciones, este campo es requerido';
    }
}
