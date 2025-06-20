<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ContractEmployeeObservation;
use App\Models\Administrative\Users\User;
use App\Http\Requests\LegalAspects\Contracts\FileUploadRequest;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Traits\ContractTrait;
use App\Traits\Filtertrait;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use DB;
use ReflectionClass;
use Validator;

class FileUploadController extends Controller
{
    use ContractTrait;
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_uploadFiles_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_uploadFiles_r, {$this->team}");
        $this->middleware("permission:contracts_uploadFiles_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_uploadFiles_d, {$this->team}", ['only' => 'destroy']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $contract_id = null;

        try
        {
            $deleteFilesConfig = ConfigurationsCompany::company($this->company)->findByKey('contracts_delete_file_upload');
        } catch (\Exception $e) {
            $deleteFilesConfig = 'NO';
        }

        $select = [];

        $select[] = "sau_ct_file_upload_contracts_leesse.id";
        $select[] = "sau_ct_file_upload_contracts_leesse.name";
        $select[] = "sau_users.name as user_name";    
        $select[] = "sau_ct_file_upload_contracts_leesse.id as user_id";    
        $select[] = "sau_ct_file_upload_contracts_leesse.created_at";
        $select[] = "sau_ct_file_upload_contracts_leesse.updated_at";
        $select[] = "sau_ct_file_upload_contracts_leesse.state";
        $select[] = "sau_ct_information_contract_lessee.social_reason AS social_reason";
        $select[] = "IFNULL(sau_ct_file_upload_contracts_leesse.module, 'Subida de Archivos') AS module";
        $select[] = "sau_ct_contract_employees.name AS employee_name";
        $select[] = "sau_ct_contract_employees.identification AS employee_identification";

        $proyectOn = $this->getProyectContract();

        if ($proyectOn == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(sau_ct_proyects.name) 
                FROM sau_ct_proyects 
                INNER JOIN sau_ct_contract_employee_proyects on sau_ct_contract_employee_proyects.proyect_contract_id = sau_ct_proyects.id
                WHERE sau_ct_contract_employee_proyects.employee_id = sau_ct_contract_employees.id
                ) AS proyects";

        $files = FileUpload::join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
        ->join('sau_ct_file_upload_contract', function ($join) 
        {
          $join->on("sau_ct_file_upload_contract.file_upload_id", "sau_ct_file_upload_contracts_leesse.id");
          $join->whereRaw("sau_ct_file_upload_contract.contract_id IN (select id from sau_ct_information_contract_lessee where company_id = {$this->company})");
        })
        ->join('sau_ct_information_contract_lessee', function ($join) 
        {
          $join->on("sau_ct_information_contract_lessee.id", "sau_ct_file_upload_contract.contract_id"); 
          $join->on("sau_ct_information_contract_lessee.company_id", "=", DB::raw("{$this->company}"));
        })
        ->leftJoin('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
        ->leftJoin('sau_ct_contract_employees', function ($join) 
        {
          $join->on("sau_ct_contract_employees.id", "sau_ct_file_document_employee.employee_id"); 
          $join->on("sau_ct_contract_employees.company_id", "=", DB::raw("{$this->company}"));
        })
        ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
        ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
        ->groupBy('sau_ct_file_upload_contracts_leesse.id')
        ->orderBy('sau_ct_file_upload_contracts_leesse.id', 'DESC');  

        $url = "/legalaspects/upload-files";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
          if (isset($filters['contracts']))
            $files->inContracts($this->getValuesForMultiselect($filters["contracts"]), $filters['filtersType']['contracts']);

          $files->inItems($this->getValuesForMultiselect($filters["items"]), $filters['filtersType']['items']);
          $files->betweenCreated($filters["dateCreate"]);
          $files->betweenUpdated($filters["dateUpdate"]);

          if (isset($filters["proyects"]) && COUNT($filters["proyects"]) > 0)
          {
            $files->inProyects($this->getValuesForMultiselect($filters["proyects"]), $filters['filtersType']['proyects']);
          }
        }

        if ($this->user->hasRole('Arrendatario', $this->company) || $this->user->hasRole('Contratista', $this->company))
        {
          $contract_id = $this->getContractIdUser($this->user->id);
          $files->where('sau_ct_information_contract_lessee.id', $contract_id);
        }

        $files->selectRaw(implode(",", $select));
        
        return Vuetable::of($files)
            ->addColumn('legalaspects-upload-files-edit', function ($file) use ($contract_id) {
              return $this->checkPermissionUserInFile($file->user_id, $contract_id);
            })
            ->addColumn('control_delete', function ($file) use ($contract_id, $deleteFilesConfig) {
              if (($file->state == 'RECHAZADO' || $file->state == 'ACEPTADO') && $deleteFilesConfig == 'NO')
                return false;                
              else
                return $this->checkPermissionUserInFile($file->user_id, $contract_id);
            })
            ->make();
    }

    public function dataOriginal()
    {
      $contract_id = null;

      try
      {
          $deleteFilesConfig = ConfigurationsCompany::company($this->company)->findByKey('contracts_delete_file_upload');
      } catch (\Exception $e) {
          $deleteFilesConfig = 'NO';
      }

      $files = FileUpload::select(
        "sau_ct_file_upload_contracts_leesse.*",
         'sau_users.name as user_name',
         'sau_ct_information_contract_lessee.social_reason AS social_reason',
         DB::raw("IF(modules.module is not null, modules.module, 'Subida de Archivos') AS module"),
         'sau_ct_contract_employees.name AS employee_name',
         'sau_ct_contract_employees.identification AS employee_identification'
      )
      ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
      ->join('sau_ct_file_upload_contract', function ($join) 
      {
        $join->on("sau_ct_file_upload_contract.file_upload_id", "sau_ct_file_upload_contracts_leesse.id");
        $join->whereRaw("sau_ct_file_upload_contract.contract_id IN (select id from sau_ct_information_contract_lessee where company_id ={$this->company})");
      })
      ->join('sau_ct_information_contract_lessee', function ($join) 
      {
        $join->on("sau_ct_information_contract_lessee.id", "sau_ct_file_upload_contract.contract_id"); 
        $join->on("sau_ct_information_contract_lessee.company_id", "=", DB::raw("{$this->company}"));
      })
      ->leftJoin('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
      ->leftJoin('sau_ct_contract_employees', function ($join) 
      {
        $join->on("sau_ct_contract_employees.id", "sau_ct_file_document_employee.employee_id"); 
        $join->on("sau_ct_contract_employees.company_id", "=", DB::raw("{$this->company}"));
      })
      ->leftJoin(DB::raw("
          (SELECT fms.* FROM sau_ct_file_module_state fms INNER JOIN sau_ct_file_upload_contracts_leesse fuc ON fuc.id = fms.file_id WHERE fms.id = (  SELECT MIN(id) FROM sau_ct_file_module_state WHERE file_id = fms.file_id)) as modules"), function ($join) {
          $join->on("modules.file_id", "sau_ct_file_upload_contracts_leesse.id");
          $join->whereRaw("modules.contract_id IN (select id from sau_ct_information_contract_lessee where company_id = {$this->company})");
      })
      ->leftJoin('sau_ct_contracts_proyects', 'sau_ct_contracts_proyects.contract_id', 'sau_ct_information_contract_lessee.id')
      ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contracts_proyects.proyect_id')
      ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_contract_employees.name', 'sau_ct_contract_employees.identification', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_module_state.file_id')
      ->orderBy('sau_ct_file_upload_contracts_leesse.id', 'DESC');

    $url = "/legalaspects/upload-files";

    $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

    if (COUNT($filters) > 0)
    {
      if (isset($filters['contracts']))
        $files->inContracts($this->getValuesForMultiselect($filters["contracts"]), $filters['filtersType']['contracts']);

      $files->inItems($this->getValuesForMultiselect($filters["items"]), $filters['filtersType']['items']);
      $files->betweenCreated($filters["dateCreate"]);
      $files->betweenUpdated($filters["dateUpdate"]);

      if (isset($filters["proyects"]))
      {
        $files->inProyects($this->getValuesForMultiselect($filters["proyects"]), $filters['filtersType']['proyects']);
      }
    }

    if ($this->user->hasRole('Arrendatario', $this->company) || $this->user->hasRole('Contratista', $this->company))
    {
      $contract_id = $this->getContractIdUser($this->user->id);
      $files->where('sau_ct_information_contract_lessee.id', $contract_id);
    }
    
    return Vuetable::of($files)
        ->addColumn('legalaspects-upload-files-edit', function ($file) use ($contract_id) {
          return $this->checkPermissionUserInFile($file->user_id, $contract_id);
        })
        ->addColumn('control_delete', function ($file) use ($contract_id, $deleteFilesConfig) {
          if (($file->state == 'RECHAZADO' || $file->state == 'ACEPTADO') && $deleteFilesConfig == 'NO')
            return false;                
          else
            return $this->checkPermissionUserInFile($file->user_id, $contract_id);
        })
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadRequest $request)
    {
      Validator::make($request->all(), [
        "name" => [
            function ($attribute, $value, $fail) 
            {
                if ($value && is_string($value))
                {
                    $exist = strpos($value, '/');

                    if ($exist)
                        $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                    else
                    {
                        $exist = strpos($value, '.');

                        if ($exist)
                            $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                    }
                }
            }
        ]
      ])->validate();

      DB::beginTransaction();

      try
      {
        $fileUpload = new FileUpload();
        $file = $request->file;
        $nameFile = base64_encode($this->user->id . now()) .'.'. $file->getClientOriginalExtension();
        
        $file->storeAs('legalAspects/files/', $nameFile,'s3');

        $fileUpload->file = $nameFile;
        $fileUpload->user_id = $this->user->id;
        $fileUpload->name = $request->name;
        $fileUpload->observations = $request->observations;
        $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');
        $fileUpload->state = $request->state == null ? 'PENDIENTE' : $request->state;
        $fileUpload->reason_rejection = $request->reason_rejection;
        $fileUpload->module = 'Subida de Archivos';
        $fileUpload->notificate = false;
      
        if(!$fileUpload->save())
        {
          return $this->respondHttp500();
        }

        if ($request->get('contract_id') && COUNT($request->get('contract_id')) > 0)
        {
          $fileUpload->contracts()->sync($this->getDataFromMultiselect($request->get('contract_id')));

          $contracts_states = $this->getDataFromMultiselect($request->get('contract_id'));

          foreach ($contracts_states as $key => $value) 
          {
            $state = new FileModuleState;
            $state->contract_id = $value;
            $state->file_id = $fileUpload->id;
            $state->module = 'Subida de Archivos';
            $state->state = 'CREADO POR CONTRATANTE';
            $state->date = date('Y-m-d');
            $state->save();
          }
        }
        else 
        {
          $fileUpload->contracts()->sync([$this->getContractIdUser($this->user->id)]);

          $state = new FileModuleState;
          $state->contract_id = $this->getContractIdUser($this->user->id);
          $state->file_id = $fileUpload->id;
          $state->module = 'Subida de Archivos';
          $state->state = 'CREADO';
          $state->date = date('Y-m-d');
          $state->save();
        }

        $this->saveLogActivitySystem('Contratistas - Archivos', 'Se creo el archivo '.$fileUpload->name);

        DB::commit();

        //$this->sendNotification($fileUpload);
      }
      catch(\Exception $e) {
        DB::rollback();
        //\Log::info($e->getMessage());
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se subio el archivo correctamente'
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      try
      {
        $fileUpload = FileUpload::findOrFail($id);
        $fileUpload->expirationDate = $fileUpload->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$fileUpload->expirationDate))->format('D M d Y');

        $type = explode('.',$fileUpload->file);
        $fileUpload->type = COUNT($type) > 1 ? $type[1] : NULL;
        $fileUpload->path = Storage::disk('s3')->url('legalAspects/files/'. $fileUpload->file);

        $contract_id = [];

        foreach ($fileUpload->contracts as $key => $value)
        {
          array_push($contract_id, $value->multiselect());
        }

        $fileUpload->contract_id = $contract_id;
        $fileUpload->multiselect_contract_id = $contract_id;

        return $this->respondHttp200([
            'data' => $fileUpload,
        ]);

      } catch(Exception $e) {
        $this->respondHttp500();
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function update(FileUploadRequest $request, FileUpload $fileUpload)
    {
      Validator::make($request->all(), [
        "name" => [
            function ($attribute, $value, $fail) 
            {
                if ($value && is_string($value))
                {
                    $exist = strpos($value, '/');

                    if ($exist)
                        $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                    else
                    {
                        $exist = strpos($value, '.');

                        if ($exist)
                            $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                    }
                }
            }
        ]
      ])->validate();
      
      DB::beginTransaction();

      try
      {
        $contract_id = $this->getContractIdUser($this->user->id);

        if (!$this->checkPermissionUserInFile($fileUpload->user_id, $contract_id))
        {
          return $this->respondWithError('No tiene permitido editar este archivo');
        }

        $file = FileUpload::find($fileUpload->id);
        $beforeFile = $file;

        if($request->file != $fileUpload->file)
        {
          $file = $request->file;
          //Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);
          $nameFile = base64_encode($this->user->id . now()) .'.'. $file->getClientOriginalExtension();
          $file->storeAs('legalAspects/files/', $nameFile,'s3');
          $fileUpload->file = $nameFile;
        }
        
        $fileUpload->name = $request->name;
        $fileUpload->observations = $request->observations;
        $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');        
        $fileUpload->state = $request->state == null ? 'PENDIENTE' : $request->state;
        $fileUpload->reason_rejection = $request->state == 'RECHAZADO' ? $request->reason_rejection : NULL;
        
        if(!$fileUpload->save()) {
          return $this->respondHttp500();
        }

        if ($request->get('contract_id') && COUNT($request->get('contract_id')) > 0)
        {
          $fileUpload->contracts()->sync($this->getDataFromMultiselect($request->get('contract_id')));
        }
        else 
        {
          $fileUpload->contracts()->sync([$this->getContractIdUser($this->user->id)]);
        }

        if(COUNT($request->get('contract_id')) == 1)
        {
          $file_old_state = FileModuleState::where('file_id', $fileUpload->id)->first();

          $fileUpload->update(
            ['module' => $file_old_state->module ? $file_old_state->module : 'Subida de archivos'])
          ;

          if ($file_old_state && $beforeFile->state != $fileUpload->state && $fileUpload->state == 'RECHAZADO')
          {
            FileModuleState::updateOrCreate(['file_id' => $fileUpload->id, 'date' => date('Y-m-d')],
            [
              'contract_id' => $this->getDataFromMultiselect($request->get('contract_id'))[0],
              'file_id' => $fileUpload->id,
              'module' => $file_old_state->module,
              'state' => 'RECHAZADO',
              'date' => date('Y-m-d')
            ]);

            $fileUpload->notificate = false;
          }
          else 
          {
            if ($beforeFile->name != $fileUpload->name || $beforeFile->file != $fileUpload->file || $beforeFile->expirationDate != $fileUpload->expirationDate)
            {
              if (!$this->user->hasRole('Arrendatario', $this->company) || !$this->user->hasRole('Contratista', $this->company))
              {
                if ($beforeFile->state != $fileUpload->state && $fileUpload->state == 'ACEPTADO')
                {
                  //notificar creador
                  FileModuleState::updateOrCreate(['file_id' => $fileUpload->id, 'date' => date('Y-m-d')],
                  [
                    'contract_id' => $this->getDataFromMultiselect($request->get('contract_id'))[0],
                    'file_id' => $fileUpload->id,
                    'module' => $file_old_state->module ? $file_old_state->module : 'Subida de archivos',
                    'state' => 'ACEPTADO',
                    'date' => date('Y-m-d')
                  ]);

                  $fileUpload->notificate = false;
                }
                else {
                  FileModuleState::updateOrCreate(['file_id' => $fileUpload->id, 'date' => date('Y-m-d')],
                  [
                    'contract_id' => $this->getDataFromMultiselect($request->get('contract_id'))[0],
                    'file_id' => $fileUpload->id,
                    'module' => $file_old_state->module ? $file_old_state->module : 'Subida de archivos',
                    'state' => 'MODIFICADO CONTRATANTE',
                    'date' => date('Y-m-d')
                  ]);
                }
              }
              else
              {
                //notificar contratante
                FileModuleState::updateOrCreate(['file_id' => $fileUpload->id, 'date' => date('Y-m-d')],
                [
                  'contract_id' => $this->getDataFromMultiselect($request->get('contract_id'))[0],
                  'file_id' => $fileUpload->id,
                  'module' => $file_old_state->module ? $file_old_state->module : 'Subida de archivos',
                  'state' => 'MODIFICADO',
                  'date' => date('Y-m-d')
                ]);
              }
            }
            else {
              if ($beforeFile->state != $fileUpload->state && $fileUpload->state == 'ACEPTADO')
              {
                //notificar creador
                FileModuleState::updateOrCreate(['file_id' => $fileUpload->id, 'date' => date('Y-m-d')],
                [
                  'contract_id' => $this->getDataFromMultiselect($request->get('contract_id'))[0],
                  'file_id' => $fileUpload->id,
                  'module' => $file_old_state->module ? $file_old_state->module : 'Subida de archivos',
                  'state' => 'ACEPTADO',
                  'date' => date('Y-m-d')
                ]);
                
                $fileUpload->notificate = false;
              }
            }
          }

          $content = [
            'employee_id' => NULL,
            'file_id' =>  $fileUpload->id
          ];

          if(!$fileUpload->update()) {
            return $this->respondHttp500();
          }

          $this->updateEmployee($content);
        }


        $this->saveLogActivitySystem('Contratistas - Archivos', 'Se edito el archivo '.$fileUpload->name);

        DB::commit();

        //$this->sendNotification($fileUpload, 'actualizado');
      }
      catch(\Exception $e) {
        DB::rollback();
        \Log::info($e->getMessage());
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se actualizo el archivo correctamente'
      ]);
    }

    public function updateEmployee($request)
    {
        if ($request['file_id'])
        {
          $file = DB::table('sau_ct_file_document_employee')->where('file_id', $request['file_id'])->first();
          
          if ($file)
            $employee = ContractEmployee::find($file->employee_id);
        }
        else if ($request['employee_id'])
        {
          $employee = ContractEmployee::find($request['employee_id']);
        }

        if ((isset($file) && $file) || (isset($employee) && $employee))
        {
          $document_states = [];
          $files_states = [];

          foreach ($employee->activities as $activity)
          {
            $activity->documents_files = $this->getFilesByActivity($activity->id, $employee->id, $employee->contract_id);

            $documents_counts = ActivityDocument::where('activity_id', $activity->id)->where('type', 'Empleado')->get();

            $documents_counts = $documents_counts->count();
            $count = 0;

            $pendiente = false;
            $rejected = false;
            $expired = false;

            foreach ($activity->documents_files as $document)
            {
                $count_files = COUNT($document['files']);

                if ($count_files > 0)
                {
                    $count_aprobe = 0;

                    foreach ($document['files'] as $key => $file) 
                    {
                        $fileUpload = FileUpload::findOrFail($file['id']);

                        if ($fileUpload->expirationDate)
                        {
                            if ($fileUpload->expirationDate > date('Y-m-d'))
                            {
                                if ($fileUpload->state == 'ACEPTADO')
                                {
                                    $count_aprobe++;
                                    $rejected = false;
                                    $pendiente = false;
                                    $expired = false;
                                }
                                else if ($fileUpload->state == 'RECHAZADO')
                                {
                                    $rejected = true;
                                    $pendiente = false;
                                    $expired = false;
                                }
                                else if ($fileUpload->state == 'PENDIENTE')
                                {
                                    $rejected = false;
                                    $pendiente = true;
                                    $expired = false;
                                }
                            }
                            else
                            {
                                if ($fileUpload->state == 'RECHAZADO')
                                {
                                    $rejected = true;
                                    $pendiente = false;
                                    $expired = true;
                                }
                                else if ($fileUpload->state == 'PENDIENTE')
                                {
                                    $rejected = false;
                                    $pendiente = true;
                                    $expired = true;
                                }
                            }
                        }
                        else
                        {
                            if ($fileUpload->state == 'ACEPTADO')
                            {
                                $count_aprobe++;
                                $rejected = false;
                                $pendiente = false;
                                $expired = false;
                            }
                            else if ($fileUpload->state == 'RECHAZADO')
                            {
                                $rejected = true;
                                $pendiente = false;
                                $expired = false;
                            }
                            else if ($fileUpload->state == 'PENDIENTE')
                            {
                                $rejected = false;
                                $pendiente = true;
                                $expired = false;
                            }
                        }

                        if ($count_files == ($key+1))
                        {
                          if ($fileUpload->state == 'ACEPTADO' && $expired)
                            array_push($files_states, 'PENDIENTE');
                          else
                            array_push($files_states, $fileUpload->state);
                        }
                    }
                    
                    if ($count_files > 0 && $count_aprobe >= $count_files)
                        $count++;
                    else if (!$pendiente && !$rejected && !$expired)
                        $count++;
                    else if ($count_files < 1)
                        $pendiente = true;
                }
            }
          }

          if (in_array('RECHAZADO', $files_states))
          {
              $employee->update(
                [ 'state' => 'Rechazado']
              );
          }
          else if ($documents_counts > $count)
          {
              $employee->update(
                [ 'state' => 'Pendiente']
              );
          }
          else if (in_array('PENDIENTE', $files_states))
          {
              $employee->update(
                [ 'state' => 'Pendiente']
              );
          }
          else if(in_array('ACEPTADO', $files_states))
          {
            $employee->update(
              [ 'state' => 'Aprobado']
            );
          }
        }
    }

    public function getFilesByActivity($activity, $employee_id, $contract_id)
    {
        $documents = ActivityDocument::where('activity_id', $activity)->where('type', 'Empleado')->get();

        if ($documents->count() > 0)
        {
            $contract = $contract_id;
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id) {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
                $document->files = [];

                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.name AS name',
                    'sau_ct_file_upload_contracts_leesse.file AS file',
                    'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                ->get();

                if ($files)
                {
                    $files->transform(function($file, $index) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->old_name = $file->file;
                        $file->expirationDate = $file->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$file->expirationDate))->format('D M d Y');

                        return $file;
                    });

                    $document->files = $files;
                }

                return $document;
            });
        }

        return $documents;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileUpload $fileUpload)
    {
      try
      {
        $contract_id = $this->getContractIdUser($this->user->id);

        if (!$this->checkPermissionUserInFile($fileUpload->user_id, $contract_id))
        {
          return $this->respondWithError('No tiene permitido eliminar este archivo');
        }

        //Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);

        $this->saveLogActivitySystem('Contratistas - Archivos', 'Se elimino el archivo '.$fileUpload->name);
        
        if(!$fileUpload->delete())
        {
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
          'message' => 'Se elimino el archivo correctamente'
        ]);
        
      }
      catch(\Exception $e) {
        return $this->respondHttp500();
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function download(FileUpload $fileUpload)
    {
      $sub_explode = explode('.',$fileUpload->file);
      $sub = count($sub_explode) > 1 ? $sub_explode[1] : '.pdf';

      $name = $fileUpload->name.'.'.$sub;
      
      if (count($sub_explode) > 1 && $name)
      {
        $name = str_replace("/", "-", $name);

        if (Storage::disk('s3')->exists('legalAspects/files/'. $fileUpload->file)) {
            return Storage::disk('s3')->download('legalAspects/files/'. $fileUpload->file, $name);
        }
      }
      else
      {
        if (Storage::disk('s3')->exists('legalAspects/files/'. $fileUpload->file)) {
          return Storage::disk('s3')->download('legalAspects/files/'. $fileUpload->file);
        }
      }

    }

    private function checkPermissionUserInFile($user_id, $contract_id)
    {
      if ($this->user->hasRole('Arrendatario', $this->company) || $this->user->hasRole('Contratista', $this->company))
      {
        if ($this->getContractIdUser($user_id) == $contract_id)
          return true;
        else
          return false;
      }

      return true;
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFilesItem(Request $request)
    {
      $contract_id = $this->getContractIdUser($this->user->id);

      $files = FileUpload::select(
        'sau_ct_file_upload_contracts_leesse.*'
      )
      ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
      ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
      ->where('sau_ct_file_upload_contract.contract_id', $contract_id)
      ->where('sau_ct_file_item_contract.item_id', $request->item_id)
      ->get();
      #->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_section_category_items.item_name');

      $files->transform(function($file, $index){
          $file->expirationDate = (Carbon::createFromFormat('Y-m-d', $file->expirationDate))->format('D M d Y');
          $file->key = Carbon::now()->timestamp + rand(1,10000);
          return $file;
      });

      return $files;
    }

    public function dataReport(Request $request)
    {
      $data = FileUpload::selectRaw("
        sau_ct_information_contract_lessee.id as id,
        sau_ct_information_contract_lessee.social_reason as contract,
        count(sau_ct_file_upload_contracts_leesse.id) as num_files,
        COUNT(IF(state = 'RECHAZADO', sau_ct_file_upload_contracts_leesse.id, NULL)) as num_reject,
        COUNT(IF(state = 'ACEPTADO', sau_ct_file_upload_contracts_leesse.id, NULL)) as num_acep,
        COUNT(IF(state = 'PENDIENTE', sau_ct_file_upload_contracts_leesse.id, NULL)) as num_pend
      ")
      ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
      ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
      ->groupBy('sau_ct_information_contract_lessee.id')
      ->orderBy('contract');
      
      if (!$this->user->hasRole('Superadmin', $this->team))
      {
          if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
          {
              $contract = $this->getContractIdUser($this->user->id);

              $data->where('sau_ct_information_contract_lessee.id', $contract);
          }
      }

      $url = "/legalaspects/upload-files/report";

      $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

      if (COUNT($filters) > 0)
      {
        if (isset($filters['contracts']))
          $data->inContracts($this->getValuesForMultiselect($filters["contracts"]), $filters['filtersType']['contracts']);

        if (isset($filters['users']))
          $data->inUsers($this->getValuesForMultiselect($filters["users"]), $filters['filtersType']['users']);

        $data->betweenCreated($filters["dateRange"]);
      }

      return Vuetable::of($data)
            ->make();
    }

    public function aproveFile(Request $request)
    {
      DB::beginTransaction();

      try
      {
        foreach ($request->activities as $key => $activity) 
        {          
          $act = json_decode($activity, true);
          foreach ($act['documents'] as $key => $document) 
          {            
            foreach ($document['files'] as $key => $fileF) 
            {       
              $file = FileUpload::find($fileF['id']);
              $beforeFile = $file;

              $beforeState = $file->state;

              $file->state = $fileF['state'] == null ? 'PENDIENTE' : $fileF['state'];
              $file->observations = $fileF['observations'];
              $file->reason_rejection = $fileF['state'] == 'RECHAZADO' ? $fileF['reason_rejection'] : NULL;
              $file->module = 'Empleados';
              $contracts = $file->contracts;
              
              if(!$file->save()) {
                return $this->respondHttp500();
              }
              
              if ($beforeState != $file->state)
              {
                $file->notificate = false;

                if(!$file->save()) {
                  return $this->respondHttp500();
                }
                
                foreach ($contracts as $key => $contract) 
                {
                  FileModuleState::updateOrCreate(
                    [
                      'file_id' => $file->id, 
                      'date' => date('Y-m-d')
                    ],
                    [
                      'contract_id' => $contract->id,
                      'file_id' => $file->id,
                      'module' => 'Empleados',
                      'state' => $file->state,
                      'date' => Carbon::now()->format('Y-m-d')
                    ]
                  );
                }
              }     
            }
          }
        }

        $employee = ContractEmployee::find($request->id);

        $this->saveObservations('App\Models\LegalAspects\Contracts\ContractEmployeeObservation', $employee, $request->new_observations, $this->user, $request->old_observations);

        $content = [
          'employee_id' => $request->id,
          'file_id' =>  NULL
        ];

        $this->updateEmployee($content);

        DB::commit();
      }
      catch(\Exception $e) {
        DB::rollback();
        \Log::info($e->getMessage());
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se actualizo el archivo correctamente'
      ]);
    }

    public function saveObservations($class, ContractEmployee $contractEmployee, $observationDescription, User $madeByUser, $observationsToUpdate = [])
    {
        try
        {
            $this->handleObservationsUpdates($class, $madeByUser, $observationsToUpdate);

            if (!$observationDescription)
                return true;

            foreach ($observationDescription as $observation)
            {
                $observation = json_decode($observation, true);

                $newobservation = (new ReflectionClass($class))->newInstanceArgs([[
                    'description' => $observation['description']
                ]]);

                $newobservation->employee_id = $contractEmployee->id;
                $newobservation->user_id = $madeByUser->id;
                $newobservation->company_id = $this->company;
                $newobservation->save();
            }

            return true;

        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * updates old observations if necessary
     * @param  array  $observationsToUpdate
     * @return void
     */
    public function handleObservationsUpdates($class, User $madeByUser, $observationsToUpdate = [])
    {
        if (!is_array($observationsToUpdate))
            return;

        foreach ($observationsToUpdate as $observation)
        {
            $observation = json_decode($observation, true);

            $oldObservation = (new ReflectionClass($class))->newInstanceArgs([])->where('id', $observation['id'])->first();

            if (!$oldObservation)
                continue;

            if ($observation['description'] != $oldObservation->description)
            {
                $oldObservation->update([
                    'description' => $observation['description'],
                    'user_id' => $madeByUser->id
                ]);                
            }
        }
    }

    public function reportEmployeeFiles(Request $request)
    {
        $contract_id = null;

        $select = [];
        
        $select[] = "sau_ct_file_upload_contracts_leesse.id AS file_id";
        $select[] = "sau_ct_information_contract_lessee.id AS id";
        $select[] = "sau_ct_file_upload_contracts_leesse.name";  
        $select[] = "sau_ct_file_upload_contracts_leesse.created_at";
        $select[] = "date_format(sau_ct_file_upload_contracts_leesse.created_at, '%Y-%m') AS date_upload";
        $select[] = "sau_ct_file_upload_contracts_leesse.state";
        $select[] = "sau_ct_information_contract_lessee.social_reason AS social_reason";
        $select[] = "sau_ct_file_upload_contracts_leesse.module AS module";
        $select[] = "sau_ct_contract_employees.name AS employee_name";
        $proyectOn = $this->getProyectContract();

        if ($proyectOn == 'SI')
        {
            $select[] = "IF(sau_ct_contract_employees.id, (SELECT GROUP_CONCAT(sau_ct_proyects.name) FROM sau_ct_proyects INNER JOIN sau_ct_contract_employee_proyects on sau_ct_contract_employee_proyects.proyect_contract_id = sau_ct_proyects.id WHERE sau_ct_contract_employee_proyects.employee_id = sau_ct_contract_employees.id), (SELECT GROUP_CONCAT(sau_ct_proyects.name) FROM sau_ct_proyects INNER JOIN sau_ct_contracts_proyects on sau_ct_contracts_proyects.proyect_id = sau_ct_proyects.id WHERE sau_ct_contracts_proyects.contract_id = sau_ct_information_contract_lessee.id)) AS proyects";
        }

        $files = FileUpload::join('sau_ct_file_upload_contract', function ($join) 
        {
          $join->on("sau_ct_file_upload_contract.file_upload_id", "sau_ct_file_upload_contracts_leesse.id");
          $join->whereRaw("sau_ct_file_upload_contract.contract_id IN (select id from sau_ct_information_contract_lessee where company_id = {$this->company})");
        })
        ->join('sau_ct_information_contract_lessee', function ($join) 
        {
          $join->on("sau_ct_information_contract_lessee.id", "sau_ct_file_upload_contract.contract_id"); 
          $join->on("sau_ct_information_contract_lessee.company_id", "=", DB::raw("{$this->company}"));
        })
        ->leftJoin('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
        ->leftJoin('sau_ct_contract_employees', function ($join) 
        {
          $join->on("sau_ct_contract_employees.id", "sau_ct_file_document_employee.employee_id"); 
          $join->on("sau_ct_contract_employees.company_id", "=", DB::raw("{$this->company}"));
        });

        if ($proyectOn == 'SI')
        {
          $files->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
          ->leftJoin('sau_ct_contracts_proyects', 'sau_ct_contracts_proyects.contract_id', 'sau_ct_information_contract_lessee.id')
          ->leftjoin('sau_ct_proyects', 'sau_ct_proyects.id', DB::raw("
            CASE WHEN sau_ct_contract_employees.id IS NOT NULL THEN sau_ct_contract_employee_proyects.proyect_contract_id ELSE sau_ct_contracts_proyects.proyect_id END"));
        }

        $files->whereIn('sau_ct_file_upload_contracts_leesse.module', ['Empleados', 'Documentos globales'])
        ->groupBy('sau_ct_file_upload_contracts_leesse.id')
        ->orderBy('sau_ct_file_upload_contracts_leesse.id', 'DESC');  

        $url = "/legalaspects/upload-files/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
          if (isset($filters['contracts']))
            $files->inContracts($this->getValuesForMultiselect($filters["contracts"]), $filters['filtersType']['contracts']);

          $files->betweenCreated($filters["dateRange"]); 
          
          if (isset($filters['users']))
            $files->inUsers($this->getValuesForMultiselect($filters["users"]), $filters['filtersType']['users']);

          if (isset($filters['modules']))
            $files->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);
        }

        if ($this->user->hasRole('Arrendatario', $this->company) || $this->user->hasRole('Contratista', $this->company))
        {
          $contract_id = $this->getContractIdUser($this->user->id);
          $files->where('sau_ct_information_contract_lessee.id', $contract_id);
        }

        $files->selectRaw(implode(",", $select));
        
        return Vuetable::of($files)
            ->make();
    }

    public function multiselectModuleFiles(Request $request)
    {
        $modules = FileUpload::select("module")
            ->orderBy('name')
            ->whereIn('sau_ct_file_upload_contracts_leesse.module', ['Empleados', 'Documentos globales'])
            ->pluck('module', 'module');

        return $this->multiSelectFormat($modules);
    }
}
