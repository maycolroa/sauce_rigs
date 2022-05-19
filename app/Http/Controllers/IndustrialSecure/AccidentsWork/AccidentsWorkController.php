<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Models\IndustrialSecure\WorkAccidents\Person;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\IndustrialSecure\AccidentWork\AccidentRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Carbon\Carbon;
use DB;

class AccidentsWorkController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:activities_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:activities_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:activities_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:activities_d, {$this->team}", ['only' => 'destroy']);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $accidents = Accident::select('*');

        return Vuetable::of($accidents)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\AccidentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccidentRequest $request)
    //public function store(Request $request)
    {
        DB::beginTransaction();

        try 
        {
            $accident = new Accident();
            $accident->company_id = $this->company;
            $accident->user_id = $this->user->id;
            $accident->tipo_vinculacion_persona = $request->tipo_vinculacion_persona;

            ///////////////Empleado//////////////

            if ($request->tipo_vinculador_laboral == "Empleador")
            {
                $employee = Employee::find($request->employee_id);

                if ($employee)
                {
                    $accident->employee_id = $employee->id;
                    $accident->tipo_identificacion_persona = 'CC';
                    $accident->nombre_persona = $employee->name;
                    $accident->identificacion_persona = $employee->identification;
                    $accident->fecha_nacimiento_persona = $employee->date_of_birth;
                    $accident->sexo_persona = $employee->sex;
                    $accident->email_persona = $employee->email;
                    $accident->employee_position_id = $employee->position->id;
                    $accident->fecha_ingreso_empresa_persona = $employee->income_date;
                }
                else
                {
                    $accident->nombre_persona = $request->nombre_persona;
                    $accident->tipo_identificacion_persona = $request->tipo_identificacion_persona;
                    $accident->identificacion_persona = $request->identificacion_persona;
                    $accident->fecha_nacimiento_persona = $request->fecha_nacimiento_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_nacimiento_persona))->format('Ymd') : NULL;
                    $accident->sexo_persona = $request->sexo_persona;
                    $accident->direccion_persona = $request->direccion_persona;
                    $accident->telefono_persona = $request->telefono_persona;
                    $accident->email_persona = $request->email_persona;
                    $accident->cargo_persona = $request->cargo_persona;
                    $accident->fecha_ingreso_empresa_persona = $request->fecha_ingreso_empresa_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_ingreso_empresa_persona))->format('Ymd') : NULL;
                }
            }
            else
            {
                $accident->nombre_persona = $request->nombre_persona;
                $accident->tipo_identificacion_persona = $request->tipo_identificacion_persona;
                $accident->identificacion_persona = $request->identificacion_persona;
                $accident->fecha_nacimiento_persona = $request->fecha_nacimiento_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_nacimiento_persona))->format('Ymd') : NULL;
                $accident->sexo_persona = $request->sexo_persona;
                $accident->direccion_persona = $request->direccion_persona;
                $accident->telefono_persona = $request->telefono_persona;
                $accident->email_persona = $request->email_persona;
                $accident->cargo_persona = $request->cargo_persona;
                $accident->fecha_ingreso_empresa_persona = $request->fecha_ingreso_empresa_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_ingreso_empresa_persona))->format('Ymd') : NULL;
            }
            
            $accident->departamento_persona_id = $request->departamento_persona_id;
            $accident->ciudad_persona_id = $request->ciudad_persona_id;
            $accident->zona_persona = $request->zona_persona;
            $accident->tiempo_ocupacion_habitual_persona = $request->tiempo_ocupacion_habitual_persona;
            $accident->salario_persona = $request->salario_persona;
            $accident->jornada_trabajo_habitual_persona = $request->jornada_trabajo_habitual_persona;


            ///////////////////////Compañia/////////////////////////////
            $accident->tipo_vinculador_laboral = $request->tipo_vinculador_laboral;
            $accident->razon_social = $request->razon_social;
            $accident->nombre_actividad_economica_sede_principal = $request->nombre_actividad_economica_sede_principal;
            $accident->tipo_identificacion_sede_principal = $request->tipo_identificacion_sede_principal;
            $accident->identificacion_sede_principal = $request->identificacion_sede_principal;
            $accident->direccion_sede_principal = $request->direccion_sede_principal;
            $accident->telefono_sede_principal = $request->telefono_sede_principal;
            $accident->email_sede_principal = $request->email_sede_principal;
            $accident->departamento_sede_principal_id = $request->departamento_sede_principal_id;
            $accident->ciudad_sede_principal_id = $request->ciudad_sede_principal_id;
            $accident->zona_sede_principal = $request->zona_sede_principal;
            $accident->info_sede_principal_misma_centro_trabajo = $request->info_sede_principal_misma_centro_trabajo == 'SI' ? true : false;
            $accident->nombre_actividad_economica_centro_trabajo = $request->nombre_actividad_economica_centro_trabajo;
            $accident->direccion_centro_trabajo = $request->direccion_centro_trabajo;
            $accident->telefono_centro_trabajo = $request->telefono_centro_trabajo;
            $accident->email_centro_trabajo = $request->email_centro_trabajo;
            $accident->departamento_centro_trabajo_id = $request->departamento_centro_trabajo_id;
            $accident->ciudad_centro_trabajo_id = $request->ciudad_centro_trabajo_id;
            $accident->zona_centro_trabajo = $request->zona_centro_trabajo;


            ////////////////////////////////Informacion basica///////////////////////
            $accident->nivel_accidente = $request->nivel_accidente;
            $accident->fecha_envio_arl = (Carbon::createFromFormat('D M d Y',$request->fecha_envio_arl))->format('Ymd');
            $accident->fecha_envio_empresa = (Carbon::createFromFormat('D M d Y',$request->fecha_envio_empresa))->format('Ymd');
            $accident->coordinador_delegado = $request->coordinador_delegado;
            $accident->cargo = $request->cargo;
            $accident->employee_eps_id = $request->employee_eps_id;
            $accident->employee_arl_id = $request->employee_arl_id;
            $accident->employee_afp_id = $request->employee_afp_id;
            $accident->tiene_seguro_social = $request->tiene_seguro_social == 'SI' ? true : false;
            $accident->nombre_seguro_social = $request->nombre_seguro_social;


            ////////////////////Informacion accidente //////////////////////////
            $accident->fecha_accidente = $request->fecha_accidente;
            $accident->jornada_accidente = $request->jornada_accidente;
            $accident->estaba_realizando_labor_habitual = $request->estaba_realizando_labor_habitual == 'SI' ? true : false;
            $accident->otra_labor_habitual = $request->otra_labor_habitual;
            $accident->total_tiempo_laborado = $request->total_tiempo_laborado;
            $accident->tipo_accidente = $request->tipo_accidente;
            $accident->departamento_accidente = $request->departamento_accidente;
            $accident->ciudad_accidente = $request->ciudad_accidente;
            $accident->zona_accidente = $request->zona_accidente;
            $accident->accidente_ocurrio_dentro_empresa = $request->accidente_ocurrio_dentro_empresa;
            $accident->causo_muerte = $request->causo_muerte == 'SI' ? true : false;
            $accident->fecha_muerte = $request->fecha_muerte ? (Carbon::createFromFormat('D M d Y',$request->fecha_muerte))->format('Ymd'): NULL;
            $accident->otro_sitio = $request->otro_sitio;
            $accident->otro_mecanismo = $request->otro_mecanismo;
            $accident->otra_lesion = $request->otra_lesion;
            $accident->agent_id = $request->agent_id;
            $accident->mechanism_id = $request->mechanism_id;
            $accident->site_id = $request->site_id;

            ///////////////////Descripcion del accidente/////////////////////////
            $accident->descripcion_accidente = $request->descripcion_accidente;
            $accident->personas_presenciaron_accidente = $request->personas_presenciaron_accidente  == 'SI' ? true : false;
            $accident->nombres_apellidos_responsable_informe = $request->nombres_apellidos_responsable_informe;
            $accident->cargo_responsable_informe = $request->cargo_responsable_informe;
            $accident->tipo_identificacion_responsable_informe = $request->tipo_identificacion_responsable_informe;
            $accident->identificacion_responsable_informe = $request->identificacion_responsable_informe;
            $accident->fecha_diligenciamiento_informe = (Carbon::createFromFormat('D M d Y',$request->fecha_diligenciamiento_informe))->format('Ymd');

            ///////////////////Observaciones y archivos
            $accident->observaciones_empresa = $request->observaciones_empresa;


            $accident->consolidado = false;
            
            if(!$accident->save()){
                return $this->respondHttp500();
            }

            $accident->partsBody()->sync($request->parts_body);
            $accident->lesionTypes()->sync($request->lesions_id);

            foreach ($request->persons['persons'] as $key => $value) {
                $person = new Person;
                $person->name = $value['name'];
                $person->position = $value['cargo'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->save();
            }

            foreach ($request->participants_investigations['persons'] as $key => $value) {
                $person = new Person;
                $person->name = $value['name'];
                $person->position = $value['cargo'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->save();
            }

            $detail_procedence = 'Formulario de accidente o incidente del empleado '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente;

            ActionPlan::user($this->user)
                    ->module('accidentsWork')
                    ->url(url('/administrative/actionplans'))
                    ->model($accident)
                    ->detailProcedence($detail_procedence)
                    ->activities($request->actionPlan)
                    ->save();

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se creo el formulario'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $accident = Accident::findOrFail($id);

            $accident->multiselect_departament = $accident->departamentPerson->multiselect();
            $accident->multiselect_departament_sede = $accident->departamentSede->multiselect();
            $accident->multiselect_departament_centro = $accident->departamentCentro->multiselect();
            $accident->multiselect_departament_accident = $accident->departamentAccident->multiselect();
            $accident->multiselect_municipality = $accident->ciudadPerson->multiselect();
            $accident->multiselect_municipality_sede = $accident->ciudadSede->multiselect();
            $accident->multiselect_municipality_centro = $accident->ciudadCentro->multiselect();
            $accident->multiselect_municipality_accident = $accident->ciudadAccident->multiselect();

            $values = $accident->lesionTypes()->pluck('sau_aw_types_lesion.id');
            $accident->lesions_id = $values;

            $values2 = $accident->partsBody()->pluck('sau_aw_parts_body.id');
            $accident->parts_body = $values2;

            $accident->multiselect_eps = $accident->eps->multiselect();
            $accident->multiselect_afp = $accident->afp->multiselect();
            $accident->multiselect_arl = $accident->arl->multiselect();

            $persons = $accident->persons()->where('rol', 'Presencio Accidente');

            $participants = $accident->persons()->where('rol', 'Miembro Investigación');

            $accident->persons = [
                'persons' => $persons,
                'delete' => []
            ];

            $accident->participants_investigations = [
                'persons' => $participants,
                'delete' => []
            ];
            
            $accident->files = [];

            return $this->respondHttp200([
                'data' => $accident
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\AccidentRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(AccidentRequest $request, Activity $activity)
    {
        $activity->fill($request->all());
        
        if(!$activity->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la actividad'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        if (count($activity->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar la actividad porque hay matrices de peligro asociadas a ella');
        }

        if(!$activity->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
           'message' => 'Se elimino la actividad'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $activities = Activity::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($options)
            ]);
        }
        else
        {
            $activities = Activity::selectRaw("
                sau_dm_activities.id as id,
                sau_dm_activities.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }
}
