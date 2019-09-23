<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Facades\ActionPlans\Facades\ActionPlan;

class MultiSelectRadioController extends Controller
{
    /**
     * Returns the possible activity states of the action plans
     *
     * @return Array
     */
    public function actionPlanStates()
    {
        return $this->multiSelectFormat(ActionPlan::getStates());
    }

    /**
     * Returns an arrangement with the type activities
     *
     * @return Array
     */
    public function dmTypeActivities()
    {
        $data = ["Rutinaria"=>"Rutinaria", "No rutinaria"=>"No rutinaria"];
        return $this->radioFormat(collect($data));
    }

    /**
     * Returns a matrix with the type of hazards generated for the hazard matrix
     *
     * @return Array
     */
    public function dmGeneratedDangers()
    {
        $data = [
            "Sitio de trabajo" => "Sitio de trabajo", 
            "Vecindad" => "Vecindad",
            "Fuera del sitio de trabajo" => "Fuera del sitio de trabajo"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    /**
     * Returns an array with the options of sexes.
     *
     * @return Array
     */
    public function sexs()
    {
        $data = ["Masculino"=>"Masculino", "Femenino"=>"Femenino"];
        return $this->multiSelectFormat(collect($data));
    }

    /**
     * Returns an arrangement with the si/no
     *
     * @return Array
     */
    public function siNo()
    {
        $data = ["SI"=>"SI", "NO"=>"NO"];
        return $this->radioFormat(collect($data));
    }

    /**
     * Returns an arrangement with the si/no
     *
     * @return Array
     */
    public function siNoSelect()
    {
        $data = ["SI"=>"SI", "NO"=>"NO"];
        return $this->multiSelectFormat(collect($data));
    }

    /**
     * Returns an arrangement with the type evaluation
     *
     * @return Array
     */
    public function ctTypesEvaluation()
    {
        $data = ["Verificación"=>"Verificación", "Seguimiento"=>"Seguimiento", "Adicional"=>"Adicional"];
        return $this->radioFormat(collect($data));
    }

    /**
     * Returns an arrangement with the roles defined
     *
     * @return Array
     */
    public function ctRoles()
    {
        $data = ["Arrendatario"=>"Arrendatario", "Contratista"=>"Contratista"];
        return $this->multiSelectFormat(collect($data));
    }

    /**
     * Returns an arrangement with the contract classifications
     *
     * @return Array
     */
    public function ctContractClassifications()
    {
        $data = ["Unidad de producción agropecuaria"=>"UPA", "Empresa"=>"Empresa"];
        return $this->multiSelectFormat(collect($data));
    }

    /**
     * Returns an arrangement with the risk classes
     *
     * @return Array
     */
    public function ctkindsRisks()
    {
        $data = [
            "Clase de riesgo I, II y III" => "Clase de riesgo I, II y III",
            "Clase de riesgo IV y V" => "Clase de riesgo IV y V",
            "Cualquier clase de riesgo" => "Cualquier clase de riesgo"
        ];

        return $this->multiSelectFormat(collect($data));
    }

    public function lmRepealed()
    {
        $data = [
            "SI" => "SI", 
            "NO" => "NO",
            "Parcial" => "Parcial"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function lmStates()
    {
        $data = [
            "Sin calificar" => "Sin calificar", 
            "En proceso" => "En proceso",
            "Terminada" => "Terminada"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function inspectRates()
    {
        $data = [
            "Bajo" => "Bajo", 
            "Medio" => "Medio",
            "Alto" => "Alto"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }
}