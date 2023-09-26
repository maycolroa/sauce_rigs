<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Models\IndustrialSecure\WorkAccidents\Person;
use App\Models\IndustrialSecure\WorkAccidents\Agent;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\FileAccident;
use App\Models\IndustrialSecure\WorkAccidents\TagsRolesParticipant;
use App\Models\IndustrialSecure\WorkAccidents\MainCause;
use App\Models\IndustrialSecure\WorkAccidents\SecondaryCause;
use App\Models\IndustrialSecure\WorkAccidents\TertiaryCause;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\IndustrialSecure\AccidentWork\AccidentRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Models\General\Company;
use App\Jobs\IndustrialSecure\AccidentsWork\AccidentsExportJob;
use App\Jobs\IndustrialSecure\AccidentsWork\AccidentPdfExportJob;
use App\Models\General\WorkCenter;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;
use App\Traits\Filtertrait;

class AccidentsWorkController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['prueba', 'getCauses']]);
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
            if(sau_aw_form_accidents.consolidado, 'NO', 'SI') AS consolidado,
            sau_users.name as user")
        ->join('sau_users', 'sau_users.id', 'sau_aw_form_accidents.user_id')
        ->where('sau_aw_form_accidents.company_id', $this->company);

        $url = "/industrialsecure/accidents";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["mechanism"]) && $filters["mechanism"])
                $accidents->inMechanisms($this->getValuesForMultiselect($filters["mechanism"]), $filters['filtersType']['mechanism']);

            if (isset($filters["agent"]) && $filters["agent"])
                $accidents->inAgents($this->getValuesForMultiselect($filters["agent"]), $filters['filtersType']['agent']);

            if (isset($filters["cargo"]) && $filters["cargo"])
                $accidents->inCargo($this->getValuesForMultiselect($filters["cargo"]), $filters['filtersType']['cargo']);

            if (isset($filters["activityEconomic"]) && $filters["activityEconomic"])
                $accidents->inActivityEconomic($this->getValuesForMultiselect($filters["activityEconomic"]), $filters['filtersType']['activityEconomic']);

            if (isset($filters["razonSocial"]) && $filters["razonSocial"])
                $accidents->inSocialReason($this->getValuesForMultiselect($filters["razonSocial"]), $filters['filtersType']['razonSocial']);

            if (isset($filters["sexs"]) && $filters["sexs"])
                $accidents->inSexs($this->getValuesForMultiselect($filters["sexs"]), $filters['filtersType']['sexs']);

            if (isset($filters["names"]) && $filters["names"])
                $accidents->inName($this->getValuesForMultiselect($filters["names"]), $filters['filtersType']['names']);

            if (isset($filters["identifications"]) && $filters["identifications"])
                $accidents->inIdentification($this->getValuesForMultiselect($filters["identifications"]), $filters['filtersType']['identifications']);

            if (isset($filters["departament"]) && $filters["departament"])
                $accidents->inDepartamentAccident($this->getValuesForMultiselect($filters["departament"]), $filters['filtersType']['departament']);

            if (isset($filters["city"]) && $filters["city"])
                $accidents->inCityAccident($this->getValuesForMultiselect($filters["city"]), $filters['filtersType']['city']);

            if (isset($filters['causoMuerte']) && COUNT($filters['causoMuerte']) > 0)
            {
                $values = $this->getValuesForMultiselect($filters['causoMuerte']);

                if ($filters['filtersType']['causoMuerte'] == 'IN')
                    $accidents->whereIn('causo_muerte', $values);

                else if ($filters['filtersType']['causoMuerte'] == 'NOT IN')
                    $accidents->whereNotIn('causo_muerte', $values);
            }

            if (isset($filters["dentroEmpresa"]) && COUNT($filters["dentroEmpresa"]) > 0)
            {
                $values2 = $this->getValuesForMultiselect($filters["dentroEmpresa"]);

                if ($filters['filtersType']['dentroEmpresa'] == 'IN')
                    $accidents->whereIn('accidente_ocurrio_dentro_empresa', $values2);

                else if ($filters['filtersType']['dentroEmpresa'] == 'NOT IN')
                    $accidents->whereNotIn('accidente_ocurrio_dentro_empresa', $values2);
            }

            if (isset($filters["dateRange"]) && $filters["dateRange"])
            {
                $dates_request = explode('/', $filters["dateRange"]);

                $dates = [];

                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $accidents->betweenDate($dates);
            }
        }

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
            //$accident->tipo_vinculacion_persona = $request->tipo_vinculacion_persona;

            ///////////////Empleado//////////////

            if ($request->tipo_vinculador_laboral == "Empleado")
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
                    $accident->cargo_persona = $employee->position->name;
                    $accident->employee_eps_id = $employee->employee_eps_id ? $employee->eps->id : null;
                    $accident->employee_arl_id = $employee->employee_arl_id ? $employee->arl->id : null;
                    $accident->employee_afp_id = $employee->employee_afp_id ? $employee->afp->id : null;
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
                    $accident->employee_eps_id = $request->employee_eps_id;
                    $accident->employee_arl_id = $request->employee_arl_id;
                    $accident->employee_afp_id = $request->employee_afp_id;
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
                $accident->employee_eps_id = $request->employee_eps_id;
                $accident->employee_arl_id = $request->employee_arl_id;
                $accident->employee_afp_id = $request->employee_afp_id;
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
            $accident->centro_trabajo_secundary_id = $request->centro_trabajo_secundary_id && $request->centro_trabajo_secundary_id != '{}' ? $request->centro_trabajo_secundary_id : null;


            /*///////////////////////////////Informacion basica///////////////////////
            $accident->tiene_seguro_social = $request->tiene_seguro_social == 'SI' ? true : false;
            $accident->nombre_seguro_social = $request->nombre_seguro_social;*/


            ////////////////////Informacion accidente //////////////////////////
            $accident->nivel_accidente = $request->nivel_accidente;
            $accident->fecha_accidente = $request->fecha_accidente;
            $accident->jornada_accidente = $request->jornada_accidente;
            $accident->estaba_realizando_labor_habitual = $request->estaba_realizando_labor_habitual == 'SI' ? true : false;
            $accident->otra_labor_habitual = $request->otra_labor_habitual;
            $accident->total_tiempo_laborado = $request->total_tiempo_laborado;
            $accident->tipo_accidente = $request->tipo_accidente;
            
            if ($request->accidente_ocurrio_dentro_empresa == 'Fuera de la empresa' && $request->tipo_vinculador_laboral != "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_accidente;
                $accident->ciudad_accidente = $request->ciudad_accidente;
                $accident->zona_accidente = $request->zona_accidente;
            }
            else if ($request->tipo_vinculador_laboral == "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_accidente;
                $accident->ciudad_accidente = $request->ciudad_accidente;
                $accident->zona_accidente = $request->zona_accidente;
            }
            else if ($request->accidente_ocurrio_dentro_empresa == 'Dentro de la empresa' && $request->tipo_vinculador_laboral != "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_sede_principal_id;
                $accident->ciudad_accidente = $request->ciudad_sede_principal_id;
                $accident->zona_accidente = $request->zona_sede_principal;
            }
            
            $accident->accidente_ocurrio_dentro_empresa = $request->accidente_ocurrio_dentro_empresa;
            $accident->causo_muerte = $request->causo_muerte == 'SI' ? true : false;
            $accident->fecha_muerte = $request->fecha_muerte ? (Carbon::createFromFormat('D M d Y',$request->fecha_muerte))->format('Ymd'): NULL;
            $accident->parts_body_id = $request->parts_body_id;
            $accident->type_lesion_id = $request->type_lesion_id;
            $accident->otro_sitio = $request->otro_sitio;
            $accident->otro_mecanismo = $request->otro_mecanismo;
            $accident->otra_lesion = $request->otra_lesion;
            $accident->agent_id = $request->agent_id;
            $accident->mechanism_id = $request->mechanism_id;
            $accident->site_id = $request->site_id;

            ///////////////////Descripcion del accidente/////////////////////////
            $accident->investigation_arl = $request->investigation_arl;
            $accident->fecha_envio_arl = $request->fecha_envio_arl ? (Carbon::createFromFormat('D M d Y',$request->fecha_envio_arl))->format('Ymd'): null;
            $accident->descripcion_accidente = $request->descripcion_accidente;
            $accident->personas_presenciaron_accidente = $request->personas_presenciaron_accidente  == 'SI' ? true : false;
            $accident->nombres_apellidos_responsable_informe = $request->nombres_apellidos_responsable_informe;
            $accident->cargo_responsable_informe = $request->cargo_responsable_informe;
            $accident->tipo_identificacion_responsable_informe = $request->tipo_identificacion_responsable_informe;
            $accident->identificacion_responsable_informe = $request->identificacion_responsable_informe;
            $accident->fecha_diligenciamiento_informe = (Carbon::createFromFormat('D M d Y',$request->fecha_diligenciamiento_informe))->format('Ymd');

            ///////////////////Observaciones y archivos
            //$accident->observaciones_empresa = $request->observaciones_empresa;


            $accident->consolidado = false;
            
            if(!$accident->save()){
                return $this->respondHttp500();
            }

            /*$accident->partsBody()->sync($request->parts_body);
            $accident->lesionTypes()->sync($request->lesions_id);*/

            /*foreach ($request->persons['persons'] as $key => $value) {
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

                $type_rol = $this->tagsPrepare($value['type_rol']);
                $this->tagsSaveSystemCompany($mark, TagsRolesParticipant::class);

                $person = new Person;
                $person->name = $value['name'];
                $person->position = $value['position'];
                $person->type_document = $value['type_document'];
                $person->document = $value['document'];
                $person->form_accident_id = $accident->id;
                $person->rol = $value['rol'];
                $person->type_rol = $type_rol->implode(',');
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
            }*/
            
            $this->saveLogActivitySystem('Investigación de accidentes', 'Se creo el registro de accidente del empleado: '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente);

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
        $get_files = FileAccident::where('form_accident_id', $accident)->where('type', '<>', DB::raw("'firm'"))->get();

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

            $accident->fecha_nacimiento_persona = $accident->fecha_nacimiento_persona ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_nacimiento_persona))->format('D M d Y') : $accident->fecha_nacimiento_persona;

            $accident->fecha_ingreso_empresa_persona = $accident->fecha_ingreso_empresa_persona ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_ingreso_empresa_persona))->format('D M d Y') : $accident->fecha_ingreso_empresa_persona;

            $accident->fecha_envio_arl = $accident->fecha_envio_arl ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_envio_arl))->format('D M d Y') : $accident->fecha_envio_arl;

            $accident->fecha_envio_empresa = $accident->fecha_envio_empresa ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_envio_empresa))->format('D M d Y') : $accident->fecha_envio_empresa;

            $accident->fecha_muerte = $accident->fecha_muerte ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_muerte))->format('D M d Y') : $accident->fecha_muerte;

            $accident->fecha_diligenciamiento_informe = $accident->fecha_diligenciamiento_informe ? (Carbon::createFromFormat('Y-m-d',$accident->fecha_diligenciamiento_informe))->format('D M d Y') : $accident->fecha_diligenciamiento_informe;

            $accident->info_sede_principal_misma_centro_trabajo = $accident->info_sede_principal_misma_centro_trabajo ? 'SI' : 'NO';
            $accident->tiene_seguro_social = $accident->tiene_seguro_social ? 'SI' : 'NO';
            $accident->estaba_realizando_labor_habitual = $accident->estaba_realizando_labor_habitual ? 'SI' : 'NO';
            $accident->causo_muerte = $accident->causo_muerte ? 'SI' : 'NO';
            $accident->personas_presenciaron_accidente = $accident->personas_presenciaron_accidente ? 'SI' : 'NO';

            $accident->multiselect_departamento_persona = $accident->departamentPerson->multiselect();
            $accident->multiselect_departament_sede = $accident->departamento_sede_principal_id ? $accident->departamentSede->multiselect() : [];
            $accident->multiselect_departament_centro = $accident->departamento_centro_trabajo_id ? $accident->departamentCentro->multiselect() : [];
            $accident->multiselect_departament_accident = $accident->departamento_accidente ? $accident->departamentAccident->multiselect() : [];
            $accident->multiselect_ciudad_persona = $accident->ciudadPerson->multiselect();
            $accident->multiselect_municipality_sede = $accident->ciudad_sede_principal_id ? $accident->ciudadSede->multiselect() : [];
            $accident->multiselect_municipality_centro = $accident->ciudad_centro_trabajo_id ? $accident->ciudadCentro->multiselect() : [];
            $accident->multiselect_municipality_accident = $accident->ciudad_accidente ? $accident->ciudadAccident->multiselect() : [];
            $accident->multiselect_center = $accident->centro_trabajo_secundary_id ? $accident->centroEmployee->multiselect() : [];

            /*$values = $accident->lesionTypes()->pluck('sau_aw_types_lesion.id');
            $accident->lesions_id = $values;

            $values2 = $accident->partsBody()->pluck('sau_aw_parts_body.id');
            $accident->parts_body = $values2;*/

            $accident->multiselect_eps = $accident->eps ? $accident->eps->multiselect() : [];
            $accident->multiselect_afp = $accident->afp ? $accident->afp->multiselect() : [];
            $accident->multiselect_arl = $accident->arl ? $accident->arl->multiselect() : [];

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
                $accident->employee_regional_id = $employee->employee_regional_id;

                /*if (!$employee->employee_regional_id)
                {
                    \Log::info('error');
                    return $this->respondHttp422('Debe completar la informacion del empleado '.$employee->name);
                    //return $this->respondWithError('Debe completar la informacion del empleado '.$employee->name);
                }*/
            }

            $accident->firm_image =  '';
            $accident->old_firm =  '';

            $firm = FileAccident::where('form_accident_id', $accident->id)->where('type', DB::raw("'firm'"))->first();

            if ($firm)
            {                
                $accident->firm_image =  $firm->path_image();
                $accident->old_firm =  $firm->file;
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
            //$accident->tipo_vinculacion_persona = $request->tipo_vinculacion_persona;

            ///////////////Empleado//////////////

            if ($request->tipo_vinculador_laboral == "Empleado")
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
                    $accident->cargo_persona = $employee->position->name;
                    $accident->employee_eps_id = $employee->eps ? $employee->eps->id : NULL;
                    $accident->employee_arl_id = $employee->arl ? $employee->arl->id : NULL;
                    $accident->employee_afp_id = $employee->afp ? $employee->afp->id : NULL;
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
                    $accident->employee_eps_id = $request->employee_eps_id;
                    $accident->employee_arl_id = $request->employee_arl_id;
                    $accident->employee_afp_id = $request->employee_afp_id;
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
                $accident->employee_eps_id = $request->employee_eps_id;
                $accident->employee_arl_id = $request->employee_arl_id;
                $accident->employee_afp_id = $request->employee_afp_id;
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
            $accident->centro_trabajo_secundary_id = $request->centro_trabajo_secundary_id;

            /*///////////////////////////////Informacion basica///////////////////////
            $accident->tiene_seguro_social = $request->tiene_seguro_social == 'SI' ? true : false;
            $accident->nombre_seguro_social = $request->nombre_seguro_social;*/


            ////////////////////Informacion accidente //////////////////////////
            $accident->nivel_accidente = $request->nivel_accidente;
            $accident->fecha_accidente = $request->fecha_accidente;
            $accident->jornada_accidente = $request->jornada_accidente;
            $accident->estaba_realizando_labor_habitual = $request->estaba_realizando_labor_habitual == 'SI' ? true : false;
            $accident->otra_labor_habitual = $request->otra_labor_habitual;
            $accident->total_tiempo_laborado = $request->total_tiempo_laborado;
            $accident->tipo_accidente = $request->tipo_accidente;

            if ($request->accidente_ocurrio_dentro_empresa == 'Fuera de la empresa' && $request->tipo_vinculador_laboral != "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_accidente;
                $accident->ciudad_accidente = $request->ciudad_accidente;
                $accident->zona_accidente = $request->zona_accidente;
            }
            else if ($request->tipo_vinculador_laboral == "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_accidente;
                $accident->ciudad_accidente = $request->ciudad_accidente;
                $accident->zona_accidente = $request->zona_accidente;
            }
            else if ($request->accidente_ocurrio_dentro_empresa == 'Dentro de la empresa' && $request->tipo_vinculador_laboral != "Independiente")
            {
                $accident->departamento_accidente = $request->departamento_sede_principal_id;
                $accident->ciudad_accidente = $request->ciudad_sede_principal_id;
                $accident->zona_accidente = $request->zona_sede_principal;
            }

            $accident->accidente_ocurrio_dentro_empresa = $request->accidente_ocurrio_dentro_empresa;
            $accident->causo_muerte = $request->causo_muerte == 'SI' ? true : false;
            $accident->fecha_muerte = $request->fecha_muerte ? (Carbon::createFromFormat('D M d Y',$request->fecha_muerte))->format('Ymd'): NULL;
            $accident->parts_body_id = $request->parts_body_id;
            $accident->type_lesion_id = $request->type_lesion_id;
            $accident->otro_sitio = $request->otro_sitio;
            $accident->otro_mecanismo = $request->otro_mecanismo;
            $accident->otra_lesion = $request->otra_lesion;
            $accident->agent_id = $request->agent_id;
            $accident->mechanism_id = $request->mechanism_id;
            $accident->site_id = $request->site_id;

            ///////////////////Descripcion del accidente/////////////////////////
            $accident->investigation_arl = $request->investigation_arl;
            $accident->fecha_envio_arl = $request->fecha_envio_arl ? (Carbon::createFromFormat('D M d Y',$request->fecha_envio_arl))->format('Ymd') : null;
            $accident->descripcion_accidente = $request->descripcion_accidente;
            $accident->personas_presenciaron_accidente = $request->personas_presenciaron_accidente  == 'SI' ? true : false;
            $accident->nombres_apellidos_responsable_informe = $request->nombres_apellidos_responsable_informe;
            $accident->cargo_responsable_informe = $request->cargo_responsable_informe;
            $accident->tipo_identificacion_responsable_informe = $request->tipo_identificacion_responsable_informe;
            $accident->identificacion_responsable_informe = $request->identificacion_responsable_informe;
            $accident->fecha_diligenciamiento_informe = (Carbon::createFromFormat('D M d Y',$request->fecha_diligenciamiento_informe))->format('Ymd');

            ///////////////////Observaciones y archivos
            //$accident->observaciones_empresa = $request->observaciones_empresa;

            $accident->consolidado = false;
            
            if(!$accident->update()){
                return $this->respondHttp500();
            }

            /*$accident->partsBody()->sync($request->parts_body);
            $accident->lesionTypes()->sync($request->lesions_id);*/

            /*foreach ($request->persons['persons'] as $key => $value) 
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
                $type_rol = $this->tagsPrepare($value['type_rol']);
                $this->tagsSaveSystemCompany($type_rol, TagsRolesParticipant::class);

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
                $person->type_rol = $type_rol->implode(',');
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
            }*/

            $this->saveLogActivitySystem('Investigación de accidentes', 'Se edito el registro de accidente del empleado: '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente);

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

            $this->saveLogDelete('Investigación de accidentes', 'Se creo el registro de accidente del empleado: '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente);


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

    public function investigation(AccidentRequest $request)
    {        
        DB::beginTransaction();

        try 
        {
            $accident = Accident::find($request->id);
            ///////////////////Observaciones y archivos
            $accident->description_details = $request->description_details;
            $accident->observaciones_empresa = $request->observaciones_empresa;

            $accident->consolidado = false;
            
            if(!$accident->update()){
                return $this->respondHttp500();
            }

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
                $type_rol = $this->tagsPrepare($value['type_rol']);
                $this->tagsSaveSystemCompany($type_rol, TagsRolesParticipant::class);

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
                $person->type_rol = $type_rol->implode(',');
                $person->save();
            }
            foreach ($request->participants_investigations['delete'] as $key => $value) {

                if (isset($value['id']))
                {
                    $person = Person::find($value['id']);
                    $person->delete();
                }
            }

            $detail_procedence = 'Reporte de accidente o incidente del empleado '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente;

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

            $this->saveLogActivitySystem('Investigación de accidentes', 'Se realizo la investigacion del evento del empleado: '.$accident->nombre_persona . ', con fecha ' . $accident->fecha_accidente);

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

    public function download(FileAccident $file)
    {
      return Storage::disk('s3')->download($file->path_donwload(), $file->name);
    }

    public function downloadPdf(Accident $accident)
    {
        $form = $this->getDataExportPdf($accident->id);

        if ($form->consolidado)
        {
            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = PDF::loadView('pdf.formularioAccidents2', ['form' => $form] );

            $pdf->setPaper('A3', 'landscape');

            return $pdf->download('formulario_accidente.pdf');
        }
        else
        {
            return $this->respondWithError('Debe completar la informacion del accidente o incidente');
        }
    }

    public function getDataExportPdf($id)
    {
        $accident = Accident::findOrFail($id);

        $accident->info_sede_principal_misma_centro_trabajo = $accident->info_sede_principal_misma_centro_trabajo ? 'SI' : 'NO';
        $accident->tiene_seguro_social = $accident->tiene_seguro_social ? 'SI' : 'NO';
        $accident->estaba_realizando_labor_habitual = $accident->estaba_realizando_labor_habitual ? 'SI' : 'NO';
        $accident->causo_muerte = $accident->causo_muerte ? 'SI' : 'NO';
        $accident->personas_presenciaron_accidente = $accident->personas_presenciaron_accidente ? 'SI' : 'NO';

        $persons = [];
        $participants = [];

        foreach ($accident->personas as $key => $value) {
            if ($value->rol == 'Presencio Accidente')
                array_push($persons, $value);
            else
                array_push($participants, $value);
        }

        $accident->persons = $persons;

        $accident->participants_investigations = $participants;

        $accident->dia_accidente = ucfirst(Carbon::parse($accident->fecha_accidente)->locale('es_ES')->dayName);
        
        $accident->files = $this->getFiles($accident->id);
        $accident->actionPlan = ActionPlan::model($accident)->prepareDataComponent();

        $images_pdf = [];
        $i = 0;
        $j = 0;

        $accident->files->transform(function($file, $indexFile) use (&$i, &$j, &$images_pdf) {
            $file->key = Carbon::now()->timestamp + rand(1,10000);
            $file->type_file = $file->type_file;
            $file->name_file = $file->name_file;
            $file->old_name = $file->file;
            $file->path = $file->path_image();
            $images_pdf[$i][$j] = ['file' => $file->path, 'type' => $file->type, 'name' => $file->name];
            $j++;

            if ($j > (3))
            {
                $i++;
                $j = 0;
            }

            return $file;
        });

        $accident->files_pdf = $images_pdf;

        $company = Company::select('*')->where('id', $this->company)->first();

        $accident->nombre_actividad_economica_sede_principal = $company->nombre_actividad_economica_sede_principal;

        $accident->razon_social = $company->name;

        $accident->tipo_identificacion_sede_principal = $company->tipo_identificacion_sede_principal;

        $accident->identificacion_sede_principal = $company->identificacion_sede_principal;

        $accident->direccion_sede_principal = $company->direccion_sede_principal;

        $accident->email_sede_principal = $company->email_sede_principal;

        $accident->telefono_sede_principal = $company->telefono_sede_principal;

        $accident->departamentSede = $company->departament ? $company->departament->name : NULL;

        $accident->ciudadSede = $company->city ? $company->city->name : NULL;

        $accident->zona_sede_principal = $company->zona_sede_principal;

        if ($accident->info_sede_principal_misma_centro_trabajo == 'NO')
        {
            $centro = WorkCenter::find($accident->centro_trabajo_secundary_id);

            $accident->nombre_actividad_economica_centro_trabajo = $centro->activity_economic;

            $accident->direccion_centro_trabajo = $centro->direction;

            $accident->telefono_centro_trabajo = $centro->telephone;

            $accident->email_centro_trabajo = $centro->email;

            $accident->departamentCentro = $centro->departamentCentro;

            $accident->ciudadCentro = $centro->ciudadCentro;

            $accident->zona_centro_trabajo = $centro->zona;
        }

        $logo = ($company && $company->logo) ? $company->logo : null;

        $accident->logo = $logo;

        return $accident;
    }

    public function multiselectIdentification()
    {
      $data = Accident::selectRaw(
        'DISTINCT identificacion_persona AS identificacion_persona'
      )
      ->where('sau_aw_form_accidents.company_id', $this->company)
      ->orderBy('identificacion_persona')
      ->get()
      ->pluck('identificacion_persona', 'identificacion_persona');

      return $this->multiSelectFormat($data);
    }

    public function multiselectName()
    {
      $data = Accident::selectRaw(
        'DISTINCT nombre_persona AS nombre_persona'
      )
      ->where('sau_aw_form_accidents.company_id', $this->company)
      ->orderBy('nombre_persona')
      ->get()
      ->pluck('nombre_persona', 'nombre_persona');

      return $this->multiSelectFormat($data);
    }

    public function multiselectSexs()
    {
        $data = ["Masculino"=>"Masculino", "Femenino"=>"Femenino", "Sin Sexo"=>"Sin Sexo"];

        return $this->multiSelectFormat(collect($data));
    }

    public function multiselectSocialReason()
    {
      $data = Accident::selectRaw(
        'DISTINCT razon_social AS razon_social'
      )
      ->where('sau_aw_form_accidents.company_id', $this->company)
      ->orderBy('razon_social')
      ->get()
      ->pluck('razon_social', 'razon_social');

      return $this->multiSelectFormat($data);
    }

    public function multiselectActivityEconomic()
    {
      $data = Accident::selectRaw(
        'DISTINCT nombre_actividad_economica_sede_principal AS nombre_actividad_economica_sede_principal'
      )
      ->where('sau_aw_form_accidents.company_id', $this->company)
      ->orderBy('nombre_actividad_economica_sede_principal')
      ->get()
      ->pluck('nombre_actividad_economica_sede_principal', 'nombre_actividad_economica_sede_principal');

      return $this->multiSelectFormat($data);
    }

    public function multiselectCargo()
    {
      $data = Accident::selectRaw(
        'DISTINCT cargo_persona AS cargo_persona'
      )
      ->where('sau_aw_form_accidents.company_id', $this->company)
      ->orderBy('cargo_persona')
      ->get()
      ->pluck('cargo_persona', 'cargo_persona');

      return $this->multiSelectFormat($data);
    }

    public function multiselectAgents()
    {
        $agents = Agent::selectRaw("
            sau_aw_agents.id as id,
            sau_aw_agents.name as name
        ")->orderBy('name')->pluck('id', 'name');

        return $this->multiSelectFormat($agents);
    }

    public function multiselectMechanisms()
    {
        $mechanisms = Mechanism::selectRaw("
            sau_aw_mechanisms.id as id,
            sau_aw_mechanisms.name as name
        ")->orderBy('name')->pluck('id', 'name');

        return $this->multiSelectFormat($mechanisms);
    }

    public function multiselectSiNo()
    {
        $data = ["SI"=>"SI", "NO"=>"NO"];

        return $this->multiSelectFormat(collect($data));
    }

    public function export(Request $request)
    {
        try
        {
            $mechanis = $request->mechanism ? $this->getValuesForMultiselect($request->mechanism) : [];

            $agents = $request->agent ? $this->getValuesForMultiselect($request->agent) : [];

            $cargos = $request->cargo ? $this->getValuesForMultiselect($request->cargo) : [];

            $activityEconomic = $request->activityEconomic ? $this->getValuesForMultiselect($request->activityEconomic) : [];

            $razonSocial = $request->razonSocial ? $this->getValuesForMultiselect($request->razonSocial) : [];

            $sexs = $request->sexs ? $this->getValuesForMultiselect($request->sexs) : [];

            $names = $request->names ? $this->getValuesForMultiselect($request->names) : [];

            $identifications = $request->identifications ? $this->getValuesForMultiselect($request->identifications) : [];

            $departament = $request->departament ? $this->getValuesForMultiselect($request->departament) : [];

            $city = $request->city ? $this->getValuesForMultiselect($request->city) : [];

            $causoMuerte = $request->causoMuerte ? $this->getValuesForMultiselect($request->causoMuerte) : [];
            $dentroEmpresa = $request->dentroEmpresa ? $this->getValuesForMultiselect($request->dentroEmpresa) : [];

            $filtersType = $request->filtersType;

            $filters = [
                'mechanism' => $mechanis,
                'agent' => $agents,
                'cargo' => $cargos,
                'activityEconomic' => $activityEconomic,
                'razonSocial' => $razonSocial,
                'sexs' => $sexs,
                'names' => $names,
                'identifications' => $identifications,
                'departament' => $departament,
                'city' => $city,
                'causoMuerte' => $causoMuerte,
                'dentroEmpresa' => $dentroEmpresa,
                'filtersType' => $filtersType
            ];

            AccidentsExportJob::dispatch($this->user, $this->company, $filters);
          
            return $this->respondHttp200();

        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function multiselectRolesParticipants(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsRolesParticipant::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where(function ($query) {
                    $query->where('system', true);
                    $query->orWhere('company_id', $this->company);
                })
                /*->where('system', true)
                ->orWhere('company_id', $this->company)*/
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        } 
        else
        {
            $tags = TagsRolesParticipant::selectRaw("
                sau_epp_tags_marks.id as id,
                sau_epp_tags_marks.name as name
            ")
            ->where('system', true)
            ->orWhere('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function saveCauses(Request $request)
    {
        $data['delete'] = json_decode($request->delete, true);
        $request->merge($data);

        if ($request->delete && count($request->delete) > 0)
        {
            foreach ($request->delete as $key => $value) 
            {
                if ($key == 0)
                {
                    if (count($value) > 0)
                    {
                        foreach ($value as $key => $cause) 
                        {
                            $causeDel = MainCause::find($cause);

                            if ($causeDel)
                                $causeDel->delete();
                        }
                    }
                }
                else if ($key == 1)
                {
                    if (count($value) > 0)
                    {
                        foreach ($value as $key => $cause) 
                        {
                            $causeDel = SecondaryCause::find($cause);

                            if ($causeDel)
                                $causeDel->delete();
                        }
                    }
                }
                else if ($key == 2)
                {
                    if (count($value) > 0)
                    {
                        foreach ($value as $key => $cause) 
                        {
                            $causeDel = TertiaryCause::find($cause);

                            if ($causeDel)
                                $causeDel->delete();
                        }
                    }
                }
            }
        }

        foreach ($request->causes as $key => $value)
        {
            $data['causes'][$key] = json_decode($value, true);
            $request->merge($data);
        }

        foreach ($request->causes as $cause)
        {
            $id = isset($cause['id']) ? $cause['id'] : NULL;
            $causeNew = MainCause::updateOrCreate(
                [
                    'id' => $id
                ], 
                [
                    'id' => $id,
                    'description' => $cause['description'],
                    'accident_id' => $request->accident_id
                ]
            );

            $this->secondaryCauses($causeNew, $cause['secondary']);
        }

        return $this->respondHttp200([
            'message' => 'Se guardaron las causas'
        ]);
    }

    private function secondaryCauses($cause, $secondaries)
    {
        foreach ($secondaries as $secondary)
        {
            $id = isset($secondary['id']) ? $secondary['id'] : NULL;
            $secondaryNew = SecondaryCause::updateOrCreate(
                [
                    'id' => $id
                ], 
                [
                    'id' => $id,
                    'description' => $secondary['description'],
                    'main_cause_id' => $cause->id
                ]
            );

            $this->saveItems($secondaryNew, $secondary['tertiary']);
        }
    }

    private function saveItems($secondary, $tertiaries)
    {
        foreach ($tertiaries as $tertiary)
        {
            $id = isset($tertiary['id']) ? $tertiary['id'] : NULL;
            $tertiaryNew = TertiaryCause::updateOrCreate(
                [
                    'id' => $id
                ], 
                [
                    'id' => $id,
                    'description' => $tertiary['description'],
                    'secondary_cause_id' => $secondary->id
                ]
            );
        }
    }

    public function getCauses(Request $request)
    {
        $isEdit = false;
        $id_tree = 0;
        $id_tree++;

        $tree = [
            "children" => [],
            'name' => "Causas",
            'value' => 15,
            'type' => "black",
            'level' => "orange",
        ];

        $causes = MainCause::where('accident_id', $request->id)->get();

        if ($causes->count() > 0)
        {
            foreach ($causes as $key => $cause) 
            {
                $id_tree++;
            
                $principal = [
                    "children" => [],
                    'name' => $cause->description,
                    'value' => 10,
                    'type' => "grey",
                    'level' => "red"
                ];

                foreach ($cause->secondary as $secondary)
                {
                    $id_tree++;

                    $secundaria = [
                        "children" => [],
                        'name' => $secondary->description,
                        'value' => 7,
                        'type' => "grey",
                        'level' => "purple"
                    ];

                    $secondary->key = Carbon::now()->timestamp + rand(1,10000);

                    foreach ($secondary->tertiary as $indexLevel => $tertiary)
                    {
                        $id_tree++;

                        $terciaria = [
                            "children" => [],
                            'name' => $tertiary->description,
                            'value' => 5,
                            'type' => "grey",
                            'level' => "blue",
                            'isPar' => ($indexLevel % 2) == 0
                        ];

                        array_push($secundaria['children'], $terciaria);

                        $tertiary->key = Carbon::now()->timestamp + rand(1,10000);
                    }

                    array_push($principal['children'], $secundaria);
                }

                array_push($tree['children'], $principal);
            }

            $isEdit = true;
        }

        $data = [
            'delete' => [
                'causes' => [],
                'secondary' => [],
                'tertiary' => []
            ],
            'causes' => $causes,
            'accident_id' => $request->id,
            'isEdit' => $isEdit,
            'treeData' => $tree
        ];

        if ($request->has('no_encrypt'))
            return $data;
        else
            return $this->respondHttp200($data);
    }

    public function prueba(Request $request)
    {
        $myRequest = new \Illuminate\Http\Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['id' => $request->id]);
        $myRequest->request->add(['no_encrypt' => true]);
        $result = $this->getCauses($myRequest);

        return view('industrialSecure.arreglar', ['data' => $result]);
    }

    public function toggleState(Accident $accident)
    {
        $data = ['consolidado' => !$accident->consolidado];

        if (!$accident->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del evento'
        ]);
    }

    public function downloadEmail(Request $request, Accident $accident)
    {
        try
        {
            $mails = $this->getDataFromMultiselect($request->get('add_email'));

            AccidentPdfExportJob::dispatch($this->user, $this->company, $accident->id, $mails);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
