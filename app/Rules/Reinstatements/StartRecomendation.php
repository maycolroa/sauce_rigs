<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class StartRecomendation implements Rule
{
   /* define if date end in recomendation */
   protected $indefinite_recommendations;
   protected $end_recommendations;
   protected $has_recommendation;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($indefinite_recommendations, $end_recommendations,$has_recommendation)
    {
        $this->indefinite_recommendations = $indefinite_recommendations;
        $this->end_recommendations = $end_recommendations;
        $this->has_recommendation = $has_recommendation;
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
        if ($this->has_recommendation == 'SI')
        {
            if ($value != null)
            {
                if ($this->indefinite_recommendations == 'NO')
                {
                    if ($this->end_recommendations != null)
                    {
                        if (Carbon::createFromFormat('D M d Y', $value)->lte(Carbon::createFromFormat('D M d Y', $this->end_recommendations)))
                        {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        return false;
                    }
                } else {
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
        if ($this->indefinite_recommendations == 'NO')
        {
            if ($this->end_recommendations != null)
                return "Debe diligenciar primero Fecha Fin Recomendaciones";
        }

        return ":attribute debe ser una fecha anterior a Fecha Fin Recomendaciones";
    }
}
