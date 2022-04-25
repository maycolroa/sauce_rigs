<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Talend;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\FileUploadRequest;
use App\Jobs\PreventiveOccupationalMedicine\Absenteeism\FileUpload\ProcessFileUploadJob;
use Carbon\Carbon;
use Validator;
use File;

class FileUploadController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        //$this->middleware("permission:absen_uploadFiles_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:absen_uploadFiles_r, {$this->team}");
        //$this->middleware("permission:absen_uploadFiles_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:absen_uploadFiles_d, {$this->team}", ['only' => 'destroy']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
      $files = FileUpload::select(
          'sau_absen_file_upload.*',
          'sau_absen_talends.name AS talend_name',
          'sau_users.name AS user_name'
      )
      ->join('sau_absen_talends', 'sau_absen_talends.id', 'sau_absen_file_upload.talend_id')
      ->join('sau_users', 'sau_users.id', 'sau_absen_file_upload.user_id');
    
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
      if (!Talend::active()->first())
        return $this->respondWithError('Aun no tiene permitido cargar archivos');

      Validator::make($request->all(), [
        "file" => [
            function ($attribute, $value, $fail)
            {
              $ext = strtolower($value->getClientOriginalExtension());
              if ($value && !is_string($value) && $ext != 'xlsx' && $ext != 'xls' && $ext != 'zip' & $ext != 'csv')
                  $fail('Archivo debe ser un Zip, un Excel o un Csv');
            },
        ]
      ])->validate();

      try
      {
        $fileUpload = new FileUpload($request->except('file'));
        $fileUpload->company_id = $this->company;
        $fileUpload->user_id = $this->user->id;

        $new_folder = Carbon::now()->format('Ymd_His');
        $fileUpload->path = "{$fileUpload->path_client()}/{$new_folder}/";
        $this->makeDirectory($fileUpload->path);

        $file = $request->file;
        $nameFile = base64_encode($this->user->id . now()) .'.'. strtolower($file->getClientOriginalExtension());
        $file->storeAs("{$fileUpload->path_client(false)}/{$new_folder}/", $nameFile, 'public');
        $file->storeAs("{$fileUpload->path_client(false)}/{$new_folder}/", $nameFile, 's3');
        $fileUpload->file = $nameFile;
        
      
        if (!$fileUpload->save())
          return $this->respondHttp500();

        ProcessFileUploadJob::dispatch($this->user, $this->company, $fileUpload, $fileUpload->talend);
      }
      catch(\Exception $e) {
        \Log::info($e->getMessage());
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
            $file = FileUpload::findOrFail($id);

            
            return $this->respondHttp200([
                'data' => $file,
            ]);
            
        } catch(Exception $e){
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
    { }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileUpload $fileUpload)
    { 
      Storage::disk('s3')->delete($fileUpload->path_delete(true));
      File::delete($fileUpload->path_delete());

      if (!$fileUpload->delete())
      {
            return $this->respondHttp500();
      }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function download(FileUpload $fileUpload)
    {
      $name = $fileUpload->name;

        if ($name)
            return Storage::disk('s3')->download($fileUpload->path_donwload(), $name);
        else
            return Storage::disk('s3')->download($fileUpload->path_donwload());
    }
}
