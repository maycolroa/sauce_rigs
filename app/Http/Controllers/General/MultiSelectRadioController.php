<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\General\Departament;
use App\Models\General\Municipality;
use App\Models\IndustrialSecure\WorkAccidents\Agent;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\PartBody;
use App\Models\IndustrialSecure\WorkAccidents\Site;
use App\Models\IndustrialSecure\WorkAccidents\TypeLesion;
use Illuminate\Http\Request;

class MultiSelectRadioController extends Controller
{
    /**
     * Returns the possible activity states of the action plans
     *
     * @return Array
     */
    public function actionPlanStates($all = '')
    {
        return $this->multiSelectFormat(ActionPlan::getStates($all));
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

    public function typesDocumentContract()
    {
        $data = [
            "Empleado" => "Empleado",
            "Contratista" => "Contratista"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function rmControlsDecrease()
    {
        $data = [
            "Frecuencia" => "Frecuencia",
            "Impacto" => "Impacto",
            "Ambos" => "Ambos"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function rmNature()
    {
        $data = [
            "Automático" => "Automático",
            "Manual" => "Manual",
            "Mixto" => "Mixto"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function rmCoverage()
    {
        $data = [
            "Inmaterial" => "Inmaterial",
            "Parcial" => "Parcial",
            "Total" => "Total"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function rmDocumentation()
    {
        $data = [
            "Documentado" => "Documentado",
            "No Documentado" => "No Documentado",
            "Parcialmente Documentado" => "Parcialmente Documentado"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function departamentsMultiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $departaments = Departament::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($departaments)
            ]);
        }
        else
        {
            $departaments = Departament::selectRaw("
                sau_departaments.id as id,
                sau_departaments.name as name
            ")->orderBy('name')->pluck('id', 'name');
        
            return $this->multiSelectFormat($departaments);
        }
    }

    public function levelRiskInspections()
    {
        $data = [
            "Alto" => "Alto",
            "Medio" => "Medio",
            "Bajo" => "Bajo"
        ];
        
        return $this->multiSelectFormat(collect($data));
    }

    public function municipalitiesMultiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $municipalities = Municipality::selectRaw(
                "sau_municipalities.id as id,
                sau_municipalities.name as name")
            ->join('sau_departaments', 'sau_departaments.id', 'sau_municipalities.departament_id')
            ->where(function ($query) use ($keyword) {
                $query->orWhere('sau_municipalities.name', 'like', $keyword);
            });

            if ($request->has('departament') && $request->get('departament') != '')
            {
                $departament = $request->get('departament');
                
                if (is_numeric($departament))
                    $municipalities->where('departament_id', $request->get('departament'));
                else
                    $municipalities->whereIn('departament_id', $this->getValuesForMultiselect($departament));
            }

            $municipalities = $municipalities->orderBy('name')->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($municipalities)
            ]);
        }
        else
        {
            $municipalities = Municipality::selectRaw(
                "GROUP_CONCAT(sau_municipalities.id) as ids,
                 sau_municipalities.name as name")
            ->join('sau_departaments', 'sau_departaments.id', 'sau_municipalities.departament_id')
            ->groupBy('sau_municipalities.name')
            ->orderBy('name')
            ->pluck('ids', 'name');
        
            return $this->multiSelectFormat($municipalities);
        }
    }

    public function agents()
    {
        $agents = Agent::selectRaw("
            sau_aw_agents.id as id,
            sau_aw_agents.name as name
        ")->orderBy('id')->pluck('id', 'name');

        return $this->radioFormat($agents);
    }

    public function sites()
    {
        $agents = Site::selectRaw("
            sau_aw_sites.id as id,
            sau_aw_sites.name as name
        ")->orderBy('id')->pluck('id', 'name');

        return $this->radioFormat($agents);
    }

    public function mechanisms()
    {
        $agents = Mechanism::selectRaw("
            sau_aw_mechanisms.id as id,
            sau_aw_mechanisms.name as name
        ")->orderBy('id')->pluck('id', 'name');

        return $this->radioFormat($agents);
    }

    public function lesiontypes()
    {
        $agents = TypeLesion::selectRaw("
            sau_aw_types_lesion.id as id,
            sau_aw_types_lesion.name as name
        ")->orderBy('id')->pluck('id', 'name');

        return $this->radioFormat($agents);
    }

    public function partsbody()
    {
        $agents = PartBody::selectRaw("
            sau_aw_parts_body.id as id,
            sau_aw_parts_body.name as name
        ")->orderBy('id')->pluck('id', 'name');

        return $this->radioFormat($agents);
    }
}