<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\General\Company;
use App\Facades\Configuration;
use App\Http\Requests\Api\CompanyRequiredRequest;
use App\Models\Administrative\Users\User;
use App\Models\General\Team;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Models\IndustrialSecure\WorkAccidents\FileAccident;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\WorkAccidents\Person;
use App\Facades\General\PermissionService;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Facades\Mail\Facades\NotificationMail;
use Auth;
use Carbon\Carbon;
use App\Traits\UtilsTrait;
use App\Models\General\Departament;
use App\Models\General\Municipality;
use App\Models\IndustrialSecure\WorkAccidents\Agent;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\PartBody;
use App\Models\IndustrialSecure\WorkAccidents\Site;
use App\Models\IndustrialSecure\WorkAccidents\TypeLesion;
use Hash;

class AccidentsController extends ApiController
{
    use UtilsTrait; 

    public function getEmployees(Request $request)
    {
      $location_level = $this->getLocationFormConfModule($request->company_id);

      $employees = Employee::select("*");
      $employees->company_scope = $request->company_id;
      $employees = $employees->get();

      $employees = $employees->map(function ($item, $keyCompany) use ($request) {
            $item->multiselect = $item->multiselect();

            return $item;
      });

      return $this->respondHttp200([
        'data' => $employees->values()
      ]);  
    }

    public function getPositions(Request $request)
    {
      $positions = EmployeePosition::select("*");
      $positions->company_scope = $request->company_id;
      $positions = $positions->get();

      $positions = $positions->map(function ($item, $keyCompany) use ($request) {
            $item->multiselect = $item->multiselect();

            return $item;
      });

      return $this->respondHttp200([
        'data' => $positions->values()
      ]);  
    }

    public function getDepartaments(Request $request)
    {
        $result = collect([]);

        $departaments = Departament::select('id', 'name');
        $departaments = $departaments->orderBy('name')->get();

        $departaments->transform(function($departament, $keyR) 
        {
            $municipalities = Municipality::select('id', 'name')
                ->where('departament_id', $departament->id)
                ->orderBy('name')
                ->get();

            $departament->municipalities = $municipalities;

            return $departament;
        });

        $result->put('departaments', $departaments);

        return $this->respondHttp200($result->toArray());
    }

    public function dataAccidents()
    {
        $result = collect([]);
        $result->put('agents', $this->agents());
        $result->put('sites', $this->sites());
        $result->put('mechanisms', $this->mechanisms());
        $result->put('lesiontypes', $this->lesiontypes());
        $result->put('partsbody', $this->partsbody());

        return $this->respondHttp200($result->toArray());
    }

    public function agents()
    {
        $result = collect([]);

        $agents = Agent::selectRaw("
            sau_aw_agents.id as id,
            sau_aw_agents.name as name
        ")
        ->orderBy('name')
        ->get();

        $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function sites()
    {
        $result = collect([]);

        $agents = Site::selectRaw("
            sau_aw_sites.id as id,
            sau_aw_sites.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function mechanisms()
    {
        $result = collect([]);

        $agents = Mechanism::selectRaw("
            sau_aw_mechanisms.id as id,
            sau_aw_mechanisms.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function lesiontypes()
    {
        $result = collect([]);

        $agents = TypeLesion::selectRaw("
            sau_aw_types_lesion.id as id,
            sau_aw_types_lesion.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function partsbody()
    {
        $result = collect([]);

        $agents = PartBody::selectRaw("
            sau_aw_parts_body.id as id,
            sau_aw_parts_body.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }



    public function inforCompanyCompleteAccidents($company_id)
    {
        $infor_company = Company::find($company_id);

        if (isset($infor_company->nombre_actividad_economica_sede_principal) && $infor_company->nombre_actividad_economica_sede_principal)
        {
            return true;
        }
        else
            return false;
    }


    public function createAccident(Request $request)
    {
        DB::beginTransaction();

        try 
        {
            $accident = new Accident();
            $accident->company_id = $request->company_id;
            $accident->user_id = $this->user->id;

            ///////////////Empleado//////////////

            if ($request->employee['type'] == "Seleccionar")
            {
                $employee = Employee::where('id', $request['employee']['employee_id']['id']);
                $employee->company_scope = $request->company_id;
                $employee = $employee->first();
            }
            else
            {
                $employee = new Employee;
                $employee->name = $request->employee['name'];
                $employee->identification = $request->employee['identification'];
                $employee->sex = $request->employee['sexo'];
                $employee->company_id = $request->company_id;
                $employee->employee_position_id = $request->employee['position_id']['id'];
                $employee->save();
            }

            if (!$employee->employee_position_id)
                return $this->respondWithError('El cargo del empleado no ha sido encontrado');

            $position_employee = EmployeePosition::where('id', $employee->employee_position_id);
            $position_employee->company_scope = $request->company_id;
            $position_employee = $position_employee->first();


            $accident->employee_id = $employee->id;
            $accident->tipo_identificacion_persona = 'CC';
            $accident->nombre_persona = $employee->name;
            $accident->identificacion_persona = $employee->identification;
            $accident->sexo_persona = $employee->sex;
            $accident->employee_position_id = $position_employee->id;
            $accident->cargo_persona = $position_employee->name;
            $accident->tipo_vinculador_laboral = 'Empleado';

            $accident->direccion_persona = $request->information_basic['direction'];
            $accident->departamento_persona_id = $request->information_basic['departament_id']['value'];
            $accident->ciudad_persona_id = $request->information_basic['municipality_id']['value'];
            $accident->zona_persona = $request->information_basic['zone'];
            $accident->tiempo_ocupacion_habitual_persona = $request->information_basic['tiempo_ocupacion_habitual_persona'];
            $accident->jornada_trabajo_habitual_persona = $request->information_basic['jornada'];

            ////////////////////Informacion accidente //////////////////////////
            $accident->nivel_accidente = $request->information_detail['nivel_accidente'];
            $accident->fecha_accidente = Carbon::createFromFormat('Y-m-d H:i:s', $request->information_detail['fecha_accidente'])->format('Y-m-d').' '.$request->information_detail['hora_accidente'];
            $accident->jornada_accidente = $request->information_detail['jornada_accidente'];
            $accident->estaba_realizando_labor_habitual = $request->information_detail['estaba_realizando_labor_habitual'] == 'SI' ? true : false;
            $accident->otra_labor_habitual = $request->information_detail['otra_labor_habitual'];
            $accident->total_tiempo_laborado = $request->information_detail['total_tiempo_laborado'];
            $accident->tipo_accidente = $request->information_detail['tipo_accidente'];
            
            if ($request->information_detail['accidente_ocurrio_dentro_empresa'] == 'Fuera de la empresa')
            {
                $accident->departamento_accidente = $request->information_detail['departamento_accidente']['value'];
                $accident->ciudad_accidente = $request->information_detail['ciudad_accidente']['value'];
                $accident->zona_accidente = $request->information_detail['zona_accidente'];
            }
            
            $accident->accidente_ocurrio_dentro_empresa = $request->information_detail['accidente_ocurrio_dentro_empresa'];
            //$accident->fecha_muerte = $request->information_detail['fecha_muerte'];
            $accident->parts_body_id = $request->information_detail['part_body_id'];
            $accident->type_lesion_id = $request->information_detail['lesion_id'];
            $accident->otro_sitio = $request->information_detail['otro_sitio'];
            $accident->otro_mecanismo = $request->information_detail['otro_mecanismo'];
            $accident->otra_lesion = $request->information_detail['otra_lesion'];
            $accident->otra_parte = $request->information_detail['otra_parte'];
            $accident->otro_agente = $request->information_detail['otro_agente'];
            $accident->agent_id = $request->information_detail['agente_id'];
            $accident->mechanism_id = $request->information_detail['mecanismo_id'];
            $accident->site_id = $request->information_detail['site_id'];


            ///////////////////Descripcion del accidente/////////////////////////
            
            $accident->personas_presenciaron_accidente = count($request->testigos) > 0 ? true : false;
            $accident->nombres_apellidos_responsable_informe = $request->firm['name'];
            $accident->cargo_responsable_informe = $request->firm['cargo'];
            $accident->tipo_identificacion_responsable_informe = 'CC';
            $accident->identificacion_responsable_informe = $request->firm['cedula'];
            $accident->descripcion_accidente = $request->descripcion_accidente;

            $accident->consolidado = false;

            $complete = $this->inforCompanyCompleteAccidents($request->company_id);

            if ($complete)
                $accident->estado_evento = 'Reportado';
            else
                $accident->estado_evento = 'Por completar';
            
            if(!$accident->save()){
                return $this->respondHttp500();
            }

            if ($request->firm['image'])
            {
                $fileUpload_firm = new FileAccident();
                $fileUpload_firm->form_accident_id = $accident->id;

                $firm = ImageApi::where('hash', $request->firm['image'])->where('type', 6)->first();

                $fileUpload_firm->file = $firm->file;
                $fileUpload_firm->name = $firm->file;
                $fileUpload_firm->type = 'firm';
                $fileUpload_firm->save();
            }

            foreach ($request->testigos as $key => $value) 
            {
                $person = new Person;
                $person->name = $value['name'];
                $person->position = $value['cargo'];
                $person->type_document = 'CC';
                $person->document = $value['cedula'];
                $person->form_accident_id = $accident->id;
                $person->rol = 'Presencio Accidente';
                $person->save();
            }
            
            $files_acc = $request->get('files');

            if ($files_acc['photo_1']['file']) 
            {
                $fileUpload_1 = new FileAccident();
                $fileUpload_1->form_accident_id = $accident->id;

                $photo_1 = ImageApi::where('hash', $files_acc['photo_1']['file'])->where('type', 6)->first();

                $fileUpload_1->file = $photo_1->file;
                $fileUpload_1->name = $photo_1->file;
                $fileUpload_1->save();
            }

            if ($files_acc['photo_4']['file']) 
            {
                $fileUpload_2 = new FileAccident();
                $fileUpload_2->form_accident_id = $accident->id;

                $photo_4 = ImageApi::where('hash', $files_acc['photo_4']['file'])->where('type', 6)->first();

                $fileUpload_2->file = $photo_4->file;
                $fileUpload_2->name = $photo_4->file;
                $fileUpload_2->save();
            }

            if ($files_acc['photo_2']['file']) 
            {
                $fileUpload_3 = new FileAccident();
                $fileUpload_3->form_accident_id = $accident->id;

                $photo_2 = ImageApi::where('hash', $files_acc['photo_2']['file'])->where('type', 6)->first();

                $fileUpload_3->file = $photo_2->file;
                $fileUpload_3->name = $photo_2->file;
                $fileUpload_3->save();
            }

            if ($files_acc['photo_3']['file']) 
            {
                $fileUpload_4 = new FileAccident();
                $fileUpload_4->form_accident_id = $accident->id;

                $photo_3 = ImageApi::where('hash', $files_acc['photo_3']['file'])->where('type', 6)->first();

                $fileUpload_4->file = $photo_3->file;
                $fileUpload_4->name = $photo_3->file;
                $fileUpload_4->save();
            }

            $form_accident = collect();
            $form_accident->push('id', $accident->id);

            $form_accident->push('employee', [
                'type' => 'Seleccionar',
                'employee_id' => $employee->id
            ]);

            $form_accident->push('descripcion_accidente', $accident->descripcion_accidente);

            $form_accident->push('information_basic', [
                'departament_id' => $request->information_basic['departament_id'],
                'municipality_id' => $request->information_basic['municipality_id'],
                'direction' => $accident->direccion_persona,
                'zone' => $accident->zona_persona,
                'jornada' => $accident->jornada_accidente,
                'tiempo_ocupacion_habitual_persona' => $accident->tiempo_ocupacion_habitual_persona,
            ]);

            $form_accident->push('information_detail', [
                'nivel_accidente' => $accident->nivel_accidente,
                'fecha_accidente' => $request->information_detail['fecha_accidente'],
                'hora_accidente' => $request->information_detail['hora_accidente'],
                'departamento_accidente' => $accident->departamento_accidente,
                'ciudad_accidente' => $accident->ciudad_accidente,
                'zona_accidente' => $accident->zona_accidente,
                'jornada_accidente' => $accident->jornada_accidente,
                'tipo_accidente' => $accident->tipo_accidente,
                'estaba_realizando_labor_habitual' => $accident->estaba_realizando_labor_habitual,
                'otra_labor_habitual' => $accident->otra_labor_habitual,
                'accidente_ocurrio_dentro_empresa' => $accident->accidente_ocurrio_dentro_empresa,
                'mecanismo_id' => $accident->mecanismo_id,
                'lesion_id' => $accident->lesion_id,
                'part_body_id' => $accident->part_body_id,
                'agente_id' => $accident->agente_id,
                'site_id' => $accident->site_id,
                'otro_mecanismo' => $accident->otro_mecanismo,
                'otra_lesion' => $accident->otra_lesion,
                'otra_parte' => $accident->otra_parte,
                'otro_agente' => $accident->otro_agente,
                'otro_sitio' => $accident->otro_sitio,
            ]);

            $form_accident->push('firm', [
                'name' => $accident->nombres_apellidos_responsable_informe,
                'cedula' => $accident->identificacion_responsable_informe,
                'cargo' => $accident->cargo_responsable_informe,
                'image' => isset($fileUpload_firm) && $fileUpload_firm ? $fileUpload_firm->path_image() : NULL,
            ]);

            foreach ($accident->personas as $key => $value) 
            {
                if ($value->rol == 'Presencio Accidente')
                {
                    $form_accident->push('testigo', [
                        'name' => $value->name,
                        'cedula' => $value->document,
                        'cargo' => $value->position
                    ]);
                }
            }

            DB::commit();

            return $this->respondHttp200([
                'data' => $form_accident
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }
}
