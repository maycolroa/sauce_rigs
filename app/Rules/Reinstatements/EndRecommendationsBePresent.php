<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class EndRecommendationsBePresent implements Rule
{

    /**
     * defines if check has recommendations
     * @var string
     */
    protected $has_recommendations;

    /**
     * defines if check has indefinite recommendations
     * @var string
     */
    protected $indefinite_recommendations;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($has_recommendations, $indefinite_recommendations)
    {
        $this->has_recommendations = $has_recommendations;
        $this->indefinite_recommendations = $indefinite_recommendations;
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
        if ($this->has_recommendations == 'SI' && $this->indefinite_recommendations == 'NO') {
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
        $hasRecommendationsLabel = trans('validation.attributes.has_recommendations');
        $indefiniteRecommendationsLabel = trans('validation.attributes.indefinite_recommendations');
        return $message = "El campo :attribute es obligatorio cuando {$hasRecommendationsLabel} es SI y {$indefiniteRecommendationsLabel} es NO.";
    }
}
