<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\TalendUpload;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\FileUploadRequest;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TalendUploadRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use Session;
use Validator;
use DB;

class FileUploadController extends Controller
{
    
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

        $files = FileUpload::select(
            'sau_absen_file_upload.*',
            'sau_users.name as user_name'
        )
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
      
      
      if($talend=TalendUpload::find(Session::get('company_id'))){

        Validator::make($request->all(), [
        "file" => [
            function ($attribute, $value, $fail)
            {
                if (($value && !is_string($value) && $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') && 
                    ($value && !is_string($value) && $value->getClientMimeType() != 'application/zip') &&
                    ($value && !is_string($value) && $value->getClientMimeType() != 'application/vnd.ms-excel'))
                    $fail('Archivo debe ser un zip o un Excel');
            },
        ]
    ])->validate();
    
    DB::beginTransaction();

      try
      {
        $fileUpload = new FileUpload();
        $file = $request->file;
        
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
        
        $file->storeAs('absenteeism/files/', $nameFile,'public');
        $file->storeAs('absenteeism/files/', $nameFile,'s3');

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
      else{
        return $this->respondHttp500();
      }
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

        $file = FileUpload::select(
          'sau_absen_file_upload.*',
          'sau_users.name as user_name'
      )
      ->join('sau_users', 'sau_users.id', 'sau_absen_file_upload.user_id')
      ->where('sau_absen_file_upload.id', $id)->first();
      
        return $this->respondHttp200([
            'data' => $file,
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
          Storage::disk('public')->delete('abasenteeism/files/'. $fileUpload->file);
          Storage::disk('s3')->delete('abasenteeism/files/'. $fileUpload->file);
          $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
          $file->storeAs('absenteeism/files/', $nameFile,'public');
          $file->storeAs('absenteeism/files/', $nameFile,'s3');
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
       
        Storage::disk('public')->delete('absenteeism/files/'. $fileUpload->file);
        Storage::disk('s3')->delete('absenteeism/files/'. $fileUpload->file);
        
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
      return Storage::disk('s3')->download('absenteeism/files/'. $fileUpload->file);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function dataTalend(Request $request)
    {
      $talend=TalendUpload::find(Session::get('company_id'));
        /*$talend = TalendUpload::select(
            'sau_absen_talends.*')->first();
      \Log::info($talend);*/
            return $this->respondHttp200([
              'data' => $talend
          ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTalend(TalendUploadRequest $request)
    {
     
      Validator::make($request->all(), [
        "file" => [
            function ($attribute, $value, $fail)
            {
                if ( ($value && !is_string($value) && $value->getClientMimeType() != 'application/zip'))
                    $fail('Archivo debe ser un zip');
            },
        ]
    ])->validate();
    
    DB::beginTransaction();

      try
      {
        $talendUpload = new TalendUpload();
        $file = $request->file;
        
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
        
        $file->storeAs('absenteeism/files/', $nameFile,'public');
        //$file->storeAs('absenteeism/files/', $nameFile,'s3');

        $talendUpload->file = $nameFile;
        $talendUpload->company_id  = Session::get('company_id');
        $talendUpload->route = "storage/app/absenteeism/files/";
        
      
        if(!$talendUpload->save())
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

}
