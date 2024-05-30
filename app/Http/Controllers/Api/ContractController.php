<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use App\Http\Requests\Api\ContractRequest;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\FileUpload;

class ContractController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->responderError('No encontrado');
    }

    public function getEmployee(ContractRequest $request)
    {        
      try {
        $contract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $request->nit)->first();

        if (!$contract)
          return $this->respondWithError('Contratista no encontrado');

        $employee = ContractEmployee::withoutGlobalScopes()->where('identification', $request->identification)->where('contract_id', $contract->id)->first();


        $parafis = collect([]);
        $cert = collect([]);
        $induc = collect([]);
        $curs = collect([]);
        $medic = collect([]);

        $parafiscales = false;
        $certificaciones = false;
        $induccion = false;
        $cursos = false;

        $parafiscales_date = false;
        $certificaciones_date = false;

        $habilitado = 0;
        $required_habilitado = 1;

        $class_document = [];


        if (in_array('Seguridad social', $class_document))
          $required_habilitado++;

        if (in_array('Certificado', $class_document))
          $required_habilitado++;

        if (in_array('Inducción', $class_document))
          $required_habilitado++;

        if (in_array('Cursos', $class_document))
          $required_habilitado++;

        foreach ($employee->activities as $key => $activity) 
        {          
          $class_document = array_merge($class_document, $activity->documents->pluck('class')->toArray());
        }

          $now = Carbon::now();

        foreach ($employee->activities as $key => $activity) 
        {
          if (in_array('Seguridad social', $class_document))
          {
            $content = $this->getFilesByActivity($activity->id, $employee->id, $contract->id, 'Seguridad social');

            if ($content && COUNT($content) > 0)
            {
              if ($content[0])
              {
                if(isset($content[0]->expirationDate) && $content[0]->expirationDate)
                {
                  $fecha = Carbon::parse($content[0]->expirationDate);

                  if ($fecha->gt($now))
                  {
                    $parafis->push($content[0]);
                    $parafiscales = true;
                    $habilitado++;
                    $parafiscales_date = false;
                    break;
                  }
                  else
                  {
                    $parafis->push($content[0]);
                    $parafiscales_date = true;
                  }
                }
                else
                {
                  $parafis->push($content[0]);
                  $parafiscales = true;
                  $parafiscales_date = false;
                  $habilitado++;
                }
              }
            }
          }
        }

        foreach ($employee->activities as $key => $activity) 
        {
          if (in_array('Certificado', $class_document))
          {
            $content = $this->getFilesByActivity($activity->id, $employee->id, $contract->id, 'Certificado');

            if ($content && COUNT($content) > 0)
            {
              if ($content[0])
              {
                if(isset($content[0]->expirationDate) && $content[0]->expirationDate)
                {
                  $fecha = Carbon::parse($content[0]->expirationDate);

                  if ($fecha->gt($now))
                  {
                    $cert->push($content[0]);
                    $certificaciones = true;
                    $habilitado++;
                    $certificaciones_date = false;
                    break;
                  }
                  else
                  {
                    $cert->push($content[0]);
                    $certificaciones_date = true;
                  }
                }
                else
                {
                  $cert->push($content[0]);
                  $certificaciones = true;
                  $certificaciones_date = false;
                  $habilitado++;
                }
              }
            }
          }            
        }

        foreach ($employee->activities as $key => $activity) 
        {
          if (in_array('Inducción', $class_document))
          {
            $content = $this->getFilesByActivity($activity->id, $employee->id, $contract->id, 'Inducción');

            if ($content && COUNT($content) > 0)
            {
              if ($content[0])
              {
                if(isset($content[0]->expirationDate) && $content[0]->expirationDate)
                {
                  $fecha = Carbon::parse($content[0]->expirationDate);

                  if ($fecha->gt($now))
                  {
                    $induc->push($content[0]);
                    $induccion = true;
                    $habilitado++;
                    break;
                  }
                }
                else
                {
                  $induc->push($content[0]);
                  $induccion = true;
                  $habilitado++;
                }
              }
            }
          }
        }

        foreach ($employee->activities as $key => $activity) 
        {
          if (in_array('Cursos', $class_document))
          {
            $content = $this->getFilesByActivity($activity->id, $employee->id, $contract->id, 'Cursos');

            if ($content && COUNT($content) > 0)
            {
              if ($content[0])
              {
                if(isset($content[0]->expirationDate) && $content[0]->expirationDate)
                {
                  $fecha = Carbon::parse($content[0]->expirationDate);

                  if ($fecha->gt($now))
                  {
                    $curs->push($content[0]);
                    $cursos = true;
                    $habilitado++;
                    break;
                  }
                }
                else
                {
                  $curs->push($content[0]);
                  $cursos = true;
                  $habilitado++;
                }
              }
            }
          }
        }

        foreach ($employee->activities as $key => $activity) 
        {
          if (in_array('Examen médico', $class_document))
          {
            $content = $this->getFilesByActivity($activity->id, $employee->id, $contract->id, 'Examen médico');

            if ($content && COUNT($content) > 0)
            {
              if ($content[0])
              {
                if(isset($content[0]->expirationDate) && $content[0]->expirationDate)
                {
                  $fecha = Carbon::parse($content[0]->expirationDate);

                  if ($fecha->gt($now))
                  {
                    $medic->push($content[0]);
                    break;
                  }
                  else
                  {
                    $medic->push($content[0]);
                  }
                }
                else
                {
                  $medic->push($content[0]);
                }
              }
            }
          }
        }

        $info_employee = [
          "documento" => $employee->identification,
          "nombre" => $employee->name,
          "direccion" => $employee->direction,
          "genero" => $employee->sex,
          "tel_residencia" => $employee->phone_residence,
          "tel_movil" => $employee->phone_movil,
          "fecha_nacimiento" => $employee->date_of_birth,
          "cargo" => $employee->position,
          "codigo_eps" => $employee->eps ? $employee->eps->code : '',
          "entidad_eps" => $employee->eps ? $employee->eps->name : '',
          "condicion_discapacidad" => $employee->disability_condition,
          "rh" => $employee->rh,
          "contacto_emergencia" => $employee->emergency_contact,
          "telefono_emergencia" => $employee->emergency_contact_phone,
          "salario" => $employee->salary,
          "arl" => $contract->arl,
          "nombre_contratista" => $contract->business_name,
          "nit_contratista" => $contract->nit,
          "centro_entrenamiento" =>  $contract->height_training_centers,
          "representante_legal" =>  $contract->legal_representative_name,
          "ok_habilitado" => $habilitado < 1 ? 'NO' : ($habilitado >= $required_habilitado ? 'SI' : 'NO'),
          "ok_parafiscales" => !$parafiscales_date && $parafiscales ? 'SI' : 'NO',
          "ok_certificaciones" => !$certificaciones_date && $certificaciones ? 'SI' : 'NO',
          "ok_induccion" => $induccion ? 'SI' : 'NO',
          "ok_cursos" => $cursos ? 'SI' : 'NO',
          "venc_seguridad_social" => $parafis->count() > 0 ? $parafis[0]->expirationDate : '',
          "fecha_venc_examedico" => $medic->count() > 0 ? $medic[0]->expirationDate : '',
          "fecha_venc_certificacion" => $cert->count() > 0 ? $cert[0]->expirationDate : '',
          "estado_civil" => $employee->civil_status,
          "jornada_laboral" => $employee->workday,
          "estado" => $employee->state_employee ? 'Activo' : 'Inactivo',
          /*"escolaridad" => "",
          "tipo_vinculacion" => "",
          "fecha_revision" => "",
          "fecha_ultima_encuesta" => "",*/
        ];

      } catch (\Exception $e) {
          \Log::info($e->getMessage());
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'data' => $info_employee
      ]);
    }

    public function getFilesByActivity($activity, $employee_id, $contract_id, $class)
    {
        $documents = ActivityDocument::where('activity_id', $activity)->where('type', 'Empleado')->where('class', $class)->get();

        $files_class = collect([]);

        if ($documents->count() > 0)
        {
            $contract = $contract_id;
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id, &$files_class) {

                $files = FileUpload::select(
                  'sau_ct_file_upload_contracts_leesse.id',
                  'sau_ct_file_upload_contracts_leesse.name',
                  'sau_ct_file_upload_contracts_leesse.expirationDate'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                //->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate > curdate()")
                ->whereRaw("sau_ct_file_upload_contracts_leesse.state = 'ACEPTADO'")
                ->orderBy('sau_ct_file_upload_contracts_leesse.id', 'DESC')
                ->first();

                $files_class->push($files);
            });
        }

        return $files_class;
    }

    public function getContract(Request $request)
    {        
      try {
        $contract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $request->nit)->first();

        if (!$contract)
          return $this->respondWithError('Contratista no encontrado');

        $info_contract = [
          "id" => $contract->id,
          "empresa_id" => $contract->company_id,
          "nombre" => $contract->social_reason,
          "nombre_comercial" => $contract->business_name,
          "nit" => $contract->nit,
          "direccion" => $contract->address,
          "telefono" => $contract->phone,
          "arl" => $contract->arl,
          "actividad_economica" => $contract->economic_activity_of_company,
          "nivel_riesgo" => $contract->risk_class,
          "fecha_creacion_empresa" => Carbon::createFromFormat('Y-m-d H:i:s', $contract->created_at)->format('Y-m-d'),
          "centro_entrenamiento" => $contract->height_training_centers,
          "representante_legal" => $contract->legal_representative_name,
          "correo" => $contract->email_contract
        ];

      } catch (\Exception $e) {
          \Log::info($e->getMessage());
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'data' => $info_contract
      ]);
    }

    public function getEmployeeIdentification(Request $request)
    {        

      if (!$request->has('identification'))
        return $this->respondWithError('Debe ingresar la identificación');

      try {

        $employee = ContractEmployee::withoutGlobalScopes()
        ->where('identification', $request->identification)
        ->where('company_id', 130)
        ->orderBy('id', 'DESC')
        ->first();

        if (!$employee)
          return $this->respondWithError('La identificación no existe en nuestro sistema');

        $info_employee = [
          "documento" => $employee->identification,
          "nombre" => $employee->name,
          "direccion" => $employee->direction,
          "genero" => $employee->sex,
          "tel_residencia" => $employee->phone_residence,
          "tel_movil" => $employee->phone_movil,
          "fecha_nacimiento" => $employee->date_of_birth,
          "codigo_eps" => $employee->eps ? $employee->eps->code : '',
          "entidad_eps" => $employee->eps ? $employee->eps->name : '',
          "condicion_discapacidad" => $employee->disability_condition,
          "rh" => $employee->rh,
          "contacto_emergencia" => $employee->emergency_contact,
          "telefono_emergencia" => $employee->emergency_contact_phone,
          "salario" => $employee->salary,
          "estado_civil" => $employee->civil_status,
          "estado" => $employee->state_employee ? 'Activo' : 'Inactivo'
        ];

      } catch (\Exception $e) {
          \Log::info($e->getMessage());
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'data' => $info_employee
      ]);
    }
  }
