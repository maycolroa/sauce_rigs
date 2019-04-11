<?php

namespace App\Http\Controllers\LegalAspects;

use App\Models\LegalAspects\FileUpload;
use App\Http\Requests\LegalApects\FileUploadRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;

class FileUploadController extends Controller
{

    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $audiometry = FileUpload::select(
            'sau_ct_file_upload_contracts_leesse.*',
            'sau_users.name as user_name'
         )->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id');
 
        return Vuetable::of($audiometry)
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
      try{
        $fileUpload = new FileUpload();
        $file = $request->file;
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();

        
        $file->storeAs('legalAspects/files/', $nameFile,'s3');

        $fileUpload->file = $nameFile;
        $fileUpload->user_id = Auth::user()->id;
        $fileUpload->name = $request->name;
        $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');
      
        if(!$fileUpload->save()){
          return $this->respondHttp500();
        }
          return $this->respondHttp200([
            'message' => 'Se subio el archivo correctamente'
        ]);
        
      }
      catch(\Exception $e){
        \Log::error($e);
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
      $fileUpload = FileUpload::findOrFail($id);

      try{
        $fileUpload->expirationDate = (Carbon::createFromFormat('Y-m-d',$fileUpload->expirationDate))->format('D M d Y');
        return $this->respondHttp200([
            'data' => $fileUpload,
        ]);
    }
    catch(Exception $e){
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
      try{

      if($request->file != $fileUpload->file){
        $file = $request->file;
        Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();

        
        $file->storeAs('legalAspects/files/', $nameFile,'s3');

        $fileUpload->file = $nameFile;
      }
      
      $fileUpload->user_id = Auth::user()->id;

      $fileUpload->name = $request->name;
      $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');
      
      if(!$fileUpload->save()){
        return $this->respondHttp500();
      }
        return $this->respondHttp200([
          'message' => 'Se actualizo el archivo correctamente'
      ]);
      
    }
    catch(\Exception $e){
      dd($e);
      \Log::error($e);
        return $this->respondHttp500();
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileUpload $fileUpload)
    {
      try{
        Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);
        $fileUpload->delete();
          return $this->respondHttp200([
            'message' => 'Se elimino el archivo correctamente'
        ]);
        
      }
      catch(\Exception $e){
        dd($e);
        \Log::error($e);
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
      return Storage::disk('s3')->download('legalAspects/files/'. $fileUpload->file);
    }
}
