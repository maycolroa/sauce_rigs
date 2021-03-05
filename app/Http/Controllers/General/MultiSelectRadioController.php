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
        $data = ["Masculino"=>"Masculino", "Femenino"=>"Femenino", "Sin Sexo"=>"Sin Sexo"];
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
            "Clase de riesgo I" => "Clase de riesgo I",
            "Clase de riesgo II" => "Clase de riesgo II",
            "Clase de riesgo III" => "Clase de riesgo III",
            "Clase de riesgo IV" => "Clase de riesgo IV",
            "Clase de riesgo V" => "Clase de riesgo V"
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

    public function phRates()
    {
        $data = [
            "Bajo" => "Bajo", 
            "Medio" => "Medio",
            "Alto" => "Alto"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function ctFileStates()
    {
        $data = [
            "ACEPTADO" => "ACEPTADO",
            "PENDIENTE" => "PENDIENTE",
            "RECHAZADO" => "RECHAZADO"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function days()
    {
        $data = [1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 12=>12, 13=>13, 14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 19=>19, 20=>20, 21=>21, 22=>22, 23=>23, 24=>24, 25=>25, 26=>26, 27=>27, 28=>28, 29=>29, 30=>30];
        
        return $this->multiSelectFormat(collect($data));
    }
}