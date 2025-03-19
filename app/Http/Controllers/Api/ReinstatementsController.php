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

        $checks = Check::selectRaw("
          employee_id AS CodigoEmpleado,
          sau_reinc_checks.id AS IdReporte,
          sau_reinc_checks.state AS Estado,
          sau_reinc_checks.deadline AS FechaCierre,
          sau_reinc_checks.motive_close AS MotivoCierre,
          sau_reinc_checks.created_at AS FechaCreacion,
          sau_employees.identification AS Identificación,
          sau_employees_regionals.name AS Empresa,
          sau_employees_headquarters.name AS Sede,
          sau_employees_processes.name AS Área,
          sau_employees_areas.name AS LugarTrabajo,
          sau_reinc_checks.disease_origin AS TipoEvento,
          sau_reinc_cie10_codes.code AS CódigoCIE10,
          sau_reinc_checks.laterality AS Lateralidad,
          sau_reinc_checks.qualification_dme AS CalificaciónDME,
          CASE WHEN has_recommendations = 'SI' THEN true ELSE false END AS TieneRecomendaciones,
          sau_reinc_checks.start_recommendations AS FechaInicioRecomendaciones,
          CASE WHEN indefinite_recommendations = 'SI' THEN true ELSE false END AS RecomendacionesIndefinidas,
          sau_reinc_checks.end_recommendations AS FechaFinRecomendaciones,
          CASE WHEN relocated = 'SI' THEN true ELSE false END AS Reubidados,
          sau_reinc_checks.monitoring_recommendations AS FechaSeguimientoRecomendaciones,
          sau_reinc_checks.origin_recommendations AS ProcedenciaRecomendaciones,
          sau_reinc_checks.detail AS DetalleRecomendaciones,
          CASE WHEN has_restrictions = 'SI' THEN true ELSE false END AS TieneRestricción,
          sau_reinc_restrictions.name AS ParteCuerpoAfectada,
          CASE WHEN in_process_origin = 'SI' THEN true ELSE false END AS EnProcesoCalificacióndeOrigen,
          CASE WHEN process_origin_done = 'SI' THEN true ELSE false END AS YasehizoprocesocalificaciónOrigen,
          sau_reinc_checks.process_origin_done_date AS FechaProcesoCalificaciónOrigen,
          sau_reinc_checks.emitter_origin AS EntidadCalificaOrigen,
          sau_reinc_checks.qualification_origin AS ClasificaciónOrigen,
          CASE WHEN in_process_pcl = 'SI' THEN true ELSE false END AS EnProcesoDePCL,
          CASE WHEN process_pcl_done = 'SI' THEN true ELSE false END AS YaSeHizoProcesoDePCL,
          sau_reinc_checks.process_pcl_done_date AS FechaProcesoPCL,
          sau_reinc_checks.pcl AS CalificaciónPCL,
          sau_reinc_checks.entity_rating_pcl AS EntidadCalificaPCL
        ")
        ->withoutGlobalScopes()
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->join('sau_reinc_restrictions', 'sau_reinc_restrictions.id', 'sau_reinc_checks.restriction_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_employees.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees.employee_area_id')
        ->where('sau_reinc_checks.company_id', 669)
        ->whereRaw("sau_reinc_checks.created_at >= '{$fechaInicio}' AND sau_reinc_checks.created_at <= '{$fechaFin}'");

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

        $checks = $checks->get()->toArray();

        if (count($checks) <= 0)
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
