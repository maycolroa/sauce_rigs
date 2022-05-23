<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Models\IndustrialSecure\WorkAccidents\Person;
use App\Models\IndustrialSecure\WorkAccidents\FileAccident;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\IndustrialSecure\AccidentWork\AccidentRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use Validator;

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
        $accidents = Accident::selectRaw(
            "sau_aw_form_accidents.*,
            if(sau_aw_form_accidents.consolidado, 'SI', 'NO') AS consolidado");

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
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                    $value->getClientMimeType() != 'image/png' && 
                    $value->getClientMimeType() != 'image/jpg' &&
                    $value->getClientMimeType() != 'image/jpeg')
                    
                    $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

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
                $accident->telefono_persona = $request->telefono_persona;
                $accident->email_persona = $request->email_persona;
                $accident->cargo_persona = $request->cargo_persona;
                $accident->fecha_ingreso_empresa_persona = $request->fecha_ingreso_empresa_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_ingreso_empresa_persona))->format('Ymd') : NULL;
            }
            
            $accident->direccion_persona = $request->direccion_persona;
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
                $person->position = $value['position'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->save();
            }

            foreach ($request->participants_investigations['persons'] as $key => $value) {
                $person = new Person;
                $person->name = $value['name'];
                $person->position = $value['position'];
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

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $accident->id);
            }

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

    public function processFiles($files, $accident_id)
    {
        $files_names_delete = [];

        foreach ($files as $keyF => $file) 
        {
            $create_file = true;

            if (isset($file['id']))
            {
                $fileUpload = FileAccident::findOrFail($file['id']);

                if (isset($file['old_name']) && $file['old_name'] == $file['file'])
                    $create_file = false;
                else
                    array_push($files_names_delete, $file['old_name']);
            }
            else
            {
                $fileUpload = new FileAccident();
                $fileUpload->form_accident_id = $accident_id;
            }

            if ($create_file)
            {
                $file_tmp = $file['file'];
                $fileUpload->name = $file_tmp->getClientOriginalName();
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                $fileUpload->file = $nameFile;
                $fileUpload->type = $file_tmp->extension();
                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
            }

            if (!$fileUpload->save())
                return $this->respondHttp500();
        }

         //Borrar archivos reemplazados
         foreach ($files_names_delete as $keyf => $file)
         {
             Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
         }
    }

    public function getFiles($accident)
    {
        $get_files = FileAccident::where('form_accident_id', $accident)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->id = $get_file->id;
                $get_file->name = $get_file->file;
                $get_file->old_name = $get_file->file;
                $get_file->path = $get_file->path_image();

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
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

            $accident->fecha_nacimiento_persona = (Carbon::createFromFormat('Y-m-d',$accident->fecha_nacimiento_persona))->format('D M d Y');

            $accident->fecha_ingreso_empresa_persona = (Carbon::createFromFormat('Y-m-d',$accident->fecha_ingreso_empresa_persona))->format('D M d Y');

            $accident->fecha_envio_arl = (Carbon::createFromFormat('Y-m-d',$accident->fecha_envio_arl))->format('D M d Y');

            $accident->fecha_envio_empresa = (Carbon::createFromFormat('Y-m-d',$accident->fecha_envio_empresa))->format('D M d Y');

            $accident->fecha_muerte = $accident->fecha_muerte ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_muerte))->format('D M d Y') : $accident->fecha_muerte;

            $accident->fecha_diligenciamiento_informe = (Carbon::createFromFormat('Y-m-d',$accident->fecha_diligenciamiento_informe))->format('D M d Y');

            $accident->info_sede_principal_misma_centro_trabajo = $accident->info_sede_principal_misma_centro_trabajo ? 'SI' : 'NO';
            $accident->tiene_seguro_social = $accident->tiene_seguro_social ? 'SI' : 'NO';
            $accident->estaba_realizando_labor_habitual = $accident->estaba_realizando_labor_habitual ? 'SI' : 'NO';
            $accident->causo_muerte = $accident->causo_muerte ? 'SI' : 'NO';
            $accident->personas_presenciaron_accidente = $accident->personas_presenciaron_accidente ? 'SI' : 'NO';

            $accident->multiselect_departamento_persona = $accident->departamentPerson->multiselect();
            $accident->multiselect_departament_sede = $accident->departamentSede->multiselect();
            $accident->multiselect_departament_centro = $accident->departamento_centro_trabajo_id ? $accident->departamentCentro->multiselect() : [];
            $accident->multiselect_departament_accident = $accident->departamentAccident->multiselect();
            $accident->multiselect_ciudad_persona = $accident->ciudadPerson->multiselect();
            $accident->multiselect_municipality_sede = $accident->ciudadSede->multiselect();
            $accident->multiselect_municipality_centro = $accident->ciudad_centro_trabajo_id ? $accident->ciudadCentro->multiselect() : [];
            $accident->multiselect_municipality_accident = $accident->ciudadAccident->multiselect();

            $values = $accident->lesionTypes()->pluck('sau_aw_types_lesion.id');
            $accident->lesions_id = $values;

            $values2 = $accident->partsBody()->pluck('sau_aw_parts_body.id');
            $accident->parts_body = $values2;

            $accident->multiselect_eps = $accident->eps->multiselect();
            $accident->multiselect_afp = $accident->afp->multiselect();
            $accident->multiselect_arl = $accident->arl->multiselect();

            $persons = [];
            $participants = [];
            foreach ($accident->personas as $key => $value) {
                if ($value->rol == 'Presencio Accidente')
                    array_push($persons, $value);
                else
                    array_push($participants, $value);
            }

            $accident->persons = [
                'persons' => $persons,
                'delete' => []
            ];

            $accident->participants_investigations = [
                'persons' => $participants,
                'delete' => []
            ];

            if ($accident->employee_id)
            {
                $employee = Employee::find($accident->employee_id);
                $accident->multiselect_employee = $employee->multiselect();
            }
            
            $accident->files = $this->getFiles($accident->id);
            $accident->actionPlan = ActionPlan::model($accident)->prepareDataComponent();

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
    public function update(AccidentRequest $request, Accident $accident)
    {        
        DB::beginTransaction();

        try 
        {
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
                $accident->telefono_persona = $request->telefono_persona;
                $accident->email_persona = $request->email_persona;
                $accident->cargo_persona = $request->cargo_persona;
                $accident->fecha_ingreso_empresa_persona = $request->fecha_ingreso_empresa_persona ? (Carbon::createFromFormat('D M d Y',$request->fecha_ingreso_empresa_persona))->format('Ymd') : NULL;
            }
            
            $accident->direccion_persona = $request->direccion_persona;
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
            
            if(!$accident->update()){
                return $this->respondHttp500();
            }

            $accident->partsBody()->sync($request->parts_body);
            $accident->lesionTypes()->sync($request->lesions_id);

            foreach ($request->persons['persons'] as $key => $value) 
            {
                if (isset($value['id']))
                    $person = Person::find($value['id']);
                else
                    $person = new Person;

                $person->name = $value['name'];
                $person->position = $value['position'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->save();
            }

            foreach ($request->persons['delete'] as $key => $value) {

                if (isset($value['id']))
                {
                    $person = Person::find($value['id']);
                    $person->delete();
                }
            }

            foreach ($request->participants_investigations['persons'] as $key => $value) 
            {
                if (isset($value['id']))
                    $person = Person::find($value['id']);
                else
                    $person = new Person;

                $person->name = $value['name'];
                $person->position = $value['position'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->save();
            }
            foreach ($request->participants_investigations['delete'] as $key => $value) {

                if (isset($value['id']))
                {
                    $person = Person::find($value['id']);
                    $person->delete();
                }
            }

            $detail_procedence = 'Formulario de accidente o incidente del empleado '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente;

            ActionPlan::user($this->user)
                    ->module('accidentsWork')
                    ->url(url('/administrative/actionplans'))
                    ->model($accident)
                    ->detailProcedence($detail_procedence)
                    ->activities($request->actionPlan)
                    ->save();

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $accident->id);
            }

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se actualizo el formulario'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accident $accident)
    { 
        DB::beginTransaction();

        try
        { 
            $get_files = FileAccident::where('form_accident_id', $accident->id)->get();

            foreach ($get_files as $key => $value) {
                Storage::disk('s3')->delete($value->path_client(false)."/".$value->file);
            }

            ActionPlan::model($accident)->modelDeleteAll();

            if(!$accident->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();
            
            return $this->respondHttp200([
            'message' => 'Se elimino el formulario'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }
        
    }

    public function download(FileAccident $file)
    {
      return Storage::disk('s3')->download($file->path_donwload(), $file->name);
    }
}
