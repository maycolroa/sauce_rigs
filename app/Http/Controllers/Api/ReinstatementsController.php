<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use App\Http\Requests\Api\CheckRequest;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\Administrative\Employees\Employee;

class ReinstatementsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
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

    public function getCheck(CheckRequest $request)
    {        
      try 
      {
        if ($request->has('fechaInicio') && $request->fechaInicio)
          $fechaInicio = $request->fechaInicio.':00';
        else
          return $this->respondWithError('El campo fechaInicio es requerido');

        
        if ($request->has('fechaFin') && $request->fechaFin)
          $fechaFin = $request->fechaFin.':59';
        else
          return $this->respondWithError('El campo fechaFin es requerido');

        $checks = Check::withoutGlobalScopes()
        ->where('company_id', 669)
        ->whereRaw("created_at >= '{$fechaInicio}' AND created_at <= '{$fechaFin}'");

        if ($request->has('codigoEmpleado') && $request->codigoEmpleado > 0)
        {
          $employee = Employee::withoutGlobalScopes()->find($request->codigoEmpleado);

          if (!$employee)
            return $this->respondWithError('El codigoEmpleado no existe en nuestro sistema');
          else
            $checks->where('employee_id', $employee->id);
        }

        if ($request->has('id') && $request->id > 0)
          $checks->where('id', $request->id);

        if ($request->has('documentoEmpleado') && $request->documentoEmpleado)
        {
          $employee = Employee::withoutGlobalScopes()->where('identification', $request->documentoEmpleado)->where('company_id', 669)->first();

          if (!$employee)
            return $this->respondWithError('El documentoEmpleado no existe en nuestro sistema');
          else
            $checks->where('employee_id', $employee->id);
        }

        $checks = $checks->get();

        if ($checks->count() <= 0)
        {
          return $this->respondWithError('No existe información registrada para los valores ingresados');
        }

        /*$info_employee = [
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
          "ok_habilitado" => $habilitado >= $required_habilitado ? 'SI' : ($required_habilitado < 1 ? 'SI' : 'NO'),
          "ok_parafiscales" => !$parafiscales_date && $parafiscales ? 'SI' : 'NO',
          "ok_certificaciones" => !$certificaciones_date && $certificaciones ? 'SI' : 'NO',
          "ok_induccion" => $induccion ? 'SI' : 'NO',
          "ok_curso_confinado" => !$cursos_date && $cursos ? 'SI' : 'NO',
          "venc_seguridad_social" => $parafis->count() > 0 ? $parafis[0]->expirationDate : '',
          "fecha_venc_examedico" => $medic->count() > 0 ? $medic[0]->expirationDate : '',
          "fecha_venc_certificacion" => $cert->count() > 0 ? $cert[0]->expirationDate : '',
          "fecha_venc_confinado" => $curs->count() > 0 ? $curs[0]->expirationDate : '',
          "estado_civil" => $employee->civil_status,
          "jornada_laboral" => $employee->workday,
          "estado" => $employee->state_employee ? 'Activo' : 'Inactivo',
          /*"escolaridad" => "",
          "tipo_vinculacion" => "",
          "fecha_revision" => "",
          "fecha_ultima_encuesta" => "",
        ];*/

      } catch (\Exception $e) {
          \Log::info($e->getMessage());
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'data' => $checks
      ]);
    }
  }
