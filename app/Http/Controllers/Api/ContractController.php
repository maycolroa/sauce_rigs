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
      \Log::info($request);
      try {
        $contract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $request->nit)->first();

        \Log::info($contract);

        if (!$contract)
          return $this->respondWithError('Contratista no encontrado');

        $employee = ContractEmployee::withoutGlobalScopes()->where('identification', $request->identification)->where('contract_id', $contract->id)->first();

        $parafiscales = false;
        $certificaciones = false;
        $induccion = false;
        $cursos = false;
        $habilitado = 0;

        foreach ($employee->activities as $key => $activity) 
        {
          
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
          "ok_habilitado" => "",
          "ok_parafiscales" => "",
          "ok_certificaciones" => "",
          "ok_induccion" => "",
          "ok_cursos" => "",
          "venc_seguridad_social" => "",
          "fecha_venc_examedico" => "",
          "fecha_venc_certificacion" => "",
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
        $acepted = false;

        $documents = ActivityDocument::where('activity_id', $activity)->where('type', 'Empleado')->where('class', $class)->get();

        if ($documents->count() > 0)
        {
            $contract = $contract_id;
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id) {
                $document->files = [];

                $files = FileUpload::select('sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                ->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate > curdate()")
                ->whereRaw("sau_ct_file_upload_contracts_leesse.state = 'ACEPTADO'")
                ->get();

                if ($files && $files->count() > 0)
                  $acepted = true;
            });
        }

        return $acepted;
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->responderError('No encontrado');
    }

  }
