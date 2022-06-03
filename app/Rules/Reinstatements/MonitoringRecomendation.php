<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class MonitoringRecomendation implements Rule
{

    protected $indefinite_recommendations;
    protected $end_recommendations;
    protected $start_recommendations;
    protected $has_recommendation;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($indefinite_recommendations, $start_recommendations, $end_recommendations, $has_recommendation)
    {
        $this->indefinite_recommendations = $indefinite_recommendations;
        $this->start_recommendations = $start_recommendations;
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
        if($this->has_recommendation == 'SI')
        {
            if ($this->start_recommendations != null)
            {
                if ($value != null)
                {
                    if (Carbon::createFromFormat('D M d Y', $value)->lte(Carbon::createFromFormat('D M d Y', $this->start_recommendations)))
                    {
                        return false;
                    } else {
                        /*if ($this->indefinite_recommendations == 'NO')
                        {
                            if (Carbon::createFromFormat('D M d Y', $value)->lt(Carbon::createFromFormat('D M d Y', $this->end_recommendations)))
                            {
                                return true;
                            }
                            else {
                                return false;
                            }
                        } else {
                            return true;
                        }*/
                        return true;
                    }
                }
                else {
                    return true;
                }
            }
            else {
                return false;
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
            return ":attribute debe ser una fecha superior a Fecha Inicio Recomendaciones";
            //return ":attribute debe ser una fecha superior a Fecha Inicio Recomendaciones y debe ser anterior a Fecha Fin Recomendaciones";
        }
        else if ($this->start_recommendations == null)
        {
            return "Debe diligenciar primero Fecha Inicio Recomendaciones";
        }
        else
        {
            return ":attribute debe ser una fecha superior a Fecha Inicio Recomendaciones";
        }
    }
}
