<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\FileUploadRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
//use App\Traits\ContractTrait;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\Mail\Facades\NotificationMail;
use Session;
use Validator;
use DB;

class FileUploadController extends Controller
{
    //use ContractTrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:absen_uploadFiles_c', ['only' => 'store']);
        $this->middleware('permission:absen_uploadFiles_r');
        $this->middleware('permission:absen_uploadFiles_u', ['only' => 'update']);
        $this->middleware('permission:absen_uploadFiles_d', ['only' => 'destroy']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        //$contract_id = null;

        $files = FileUpload::selectRaw(
            'sau_absen_file_upload.*,
            sau_users.name as user_name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_absen_file_upload.user_id');
      //->where('sau_users.id', Auth::user()->id);
          
          return Vuetable::of($files)
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
     
      /*Validator::make($request->all(), [
        "file" => [
            function ($attribute, $value, $fail)
            {
                if (($value && !is_string($value) && $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') && 
                    ($value && !is_string($value) && $value->getClientMimeType() != 'application/zip') &&
                    ($value && !is_string($value) && $value->getClientMimeType() != 'application/vnd.ms-excel'))
                    $fail('Archivo debe ser un zip o un Excel');
            },
        ]
    ])->validate();*/
    
    DB::beginTransaction();

      try
      {
        $fileUpload = new FileUpload();
        $file = $request->file;
        
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
        
        $file->storeAs('absenteeism/files/', $nameFile,'local');

        $fileUpload->file = $nameFile;
        $fileUpload->user_id = Auth::user()->id;
        $fileUpload->name = $request->name;
        
      
        if(!$fileUpload->save())
        {
          return $this->respondHttp500();
        }
                
        DB::commit();

      }
      catch(\Exception $e) {
        DB::rollback();
        //return $e->getMessage();
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
      DB::beginTransaction();

      try
      {
        
        if($request->file != $fileUpload->file)
        {
          $file = $request->file;
          Storage::disk('local')->delete('abasenteeism/files/'. $fileUpload->file);
          $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
          $file->storeAs('absenteeism/files/', $nameFile,'local');
          $fileUpload->file = $nameFile;
        }
        
        $fileUpload->name = $request->name;
        
        if(!$fileUpload->save()) {
          return $this->respondHttp500();
        }

        DB::commit();

      }
      catch(\Exception $e) {
        DB::rollback();
        //return $e->getMessage();
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se actualizo el archivo correctamente'
      ]);
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
       
        Storage::disk('local')->delete('absenteeism/files/'. $fileUpload->file);
        
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
      return Storage::disk('local')->download('absenteeism/files/'. $fileUpload->file);
    }

    private function checkPermissionUserInFile($user_id, $contract_id)
    {
      if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
      {
        if ($this->getContractIdUser($user_id) == $contract_id)
          return true;
        else
          return false;
      }

      return true;
    }

    private function sendNotification($fileUpload, $type_action = 'creado')
    {
      if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
      {
        $subject = "Contratistas - Archivo Cargado";
        $message = "Un(a) arrendatario/contratista ha $type_action un archivo, para porder verlo haga click en el botón que se encuentra abajo";
        $recipients = $this->getUsersMasterContract(Session::get('company_id'));
      }
      else 
      {
        $subject = "Contratistas - Archivo Compartido";
        $message = "Se ha $type_action un archivo compartido con su contratista, para porder verlo haga click en el botón que se encuentra abajo";

        $recipients = new Collection();

        foreach ($fileUpload->contracts as $contract)
        {
          $recipients = $recipients->merge($this->getUsersContract($contract->id));
        }
      }
      
      NotificationMail::
        subject($subject)
        ->recipients($recipients)
        ->message($message)
        ->buttons([['text'=>'Llevarme al sitio', 'url'=>url("/legalaspects/upload-files/view/{$fileUpload->id}")]])
        ->module('contracts')
        ->company(Session::get('company_id'))
        ->send();
    }
}
