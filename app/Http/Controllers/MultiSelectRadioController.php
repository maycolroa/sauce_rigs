<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class MultiSelectRadioController extends Controller
{
    /**
     * Returns the possible activity states of the action plans
     *
     * @return Array
     */
    public function actionPlanStates()
    {
        return $this->multiSelectFormat($this->getActionPlanStates());
    }

    /**
     * Returns an arrangement with the type activities
     *
     * @return Array
     */
    public function dmTypeActivities()
    {
        $activities = ["Rutinaria"=>"Rutinaria", "No rutinaria"=>"No rutinaria"];
        return $this->radioFormat(collect($activities));
    }

    /**
     * Returns a matrix with the type of hazards generated for the hazard matrix
     *
     * @return Array
     */
    public function dmGeneratedDangers()
    {
        $dangers = [
            "Sitio de trabajo" => "Sitio de trabajo", 
            "Vecindad" => "Vecindad",
            "Fuera del sitio de trabajo" => "Fuera del sitio de trabajo"
        ];
        
        return $this->multiSelectFormat(collect($dangers));
    }

    /**
     * Returns an array with the options of sexes.
     *
     * @return Array
     */
    public function sexs()
    {
        $sexs = ["Masculino"=>"M", "Femenino"=>"F"];
        return $this->multiSelectFormat(collect($sexs));
    }

    /**
     * Returns an arrangement with the si/no
     *
     * @return Array
     */
    public function siNo()
    {
        $options = ["SI"=>"SI", "NO"=>"NO"];
        return $this->radioFormat(collect($options));
    }
}