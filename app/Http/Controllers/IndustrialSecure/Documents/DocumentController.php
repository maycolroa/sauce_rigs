<?php

namespace App\Http\Controllers\IndustrialSecure\Documents;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Traits\Filtertrait;
use Illuminate\Database\Eloquent\Collection;
use App\Models\IndustrialSecure\Documents\Document;
use App\Http\Requests\IndustrialSecure\Documents\DocumentRequest;
use DB;

class DocumentController extends Controller
{
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:documentsSecurity_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:documentsSecurity_r, {$this->team}");
        $this->middleware("permission:documentsSecurity_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:documentsSecurity_d, {$this->team}", ['only' => 'destroy']);*/
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $roles = [];

        foreach ($this->user->multiselectRoles($this->team) as $key => $value) 
        {
            array_push($roles, $value['value']);
        }

        $roles = implode(',', $roles);

        $files = Document::selectRaw(
            'sau_documents_security.name,
             sau_documents_security.updated_at,
             sau_documents_security.id,
             sau_users.name as user_name'
          )
          ->join('sau_users','sau_users.id','sau_documents_security.user_creator_id')
          ->leftJoin('sau_document_security_users', 'sau_document_security_users.document_security_id', 'sau_documents_security.id')
          ->leftJoin('sau_document_security_roles', 'sau_document_security_roles.document_security_id', 'sau_documents_security.id')
          ->where(function ($q) use ($roles) {
            $q->orWhereRaw("sau_document_security_users.user_id = {$this->user->id}")
            ->orWhere('sau_documents_security.user_creator_id', $this->user->id)
            ->orWhereRaw("sau_document_security_roles.role_id in ($roles)");
          })
          ->groupBy('sau_documents_security.id')
          ->orderBy('id', 'DESC');
        
        return Vuetable::of($files)
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
      DB::beginTransaction();

      try
      {
        $document = new Document();
        $file = $request->file;
        $nameFile = base64_encode($this->user->id . now()) .'.'. $file->getClientOriginalExtension();
        
        $file->storeAs('industrialSecure/documents/files/', $nameFile,'s3');

        $document->file = $nameFile;
        $document->user_creator_id = $this->user->id;
        $document->name = $request->name;
        $document->company_id = $this->company;
      
        if(!$document->save())
        {
          return $this->respondHttp500();
        }

        if ($request->get('users_id') && COUNT($request->get('users_id')) > 0)
        {
          $document->users()->sync($this->getDataFromMultiselect($request->get('users_id')));
        }

        if ($request->get('roles_id') && COUNT($request->get('roles_id')) > 0)
        {
          $document->roles()->sync($this->getDataFromMultiselect($request->get('roles_id')));
        }

        DB::commit();

      }
      catch(\Exception $e) {
        DB::rollback();
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
     * @param  \App\Document  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      try
      {
        $document = Document::findOrFail($id);
        $users_id = [];
        $roles_id = [];

        foreach ($document->users as $key => $value)
        {
          array_push($users_id, $value->multiselect());
        }

        foreach ($document->roles as $key => $value)
        {
          array_push($roles_id, $value->multiselect());
        }

        $document->users_id = $users_id;
        $document->multiselect_user_id = $users_id;

        $document->roles_id = $roles_id;
        $document->multiselect_role = $roles_id;

        return $this->respondHttp200([
            'data' => $document,
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
    public function update(DocumentRequest $request, Document $document)
    {
      DB::beginTransaction();

      try
      {
        $file = Document::find($document->id);
        $beforeFile= $file;

        if($request->file != $document->file)
        {
          $file = $request->file;
          Storage::disk('s3')->delete('industrialSecure/documents/files/'. $document->file);
          $nameFile = base64_encode($this->user->id . now()) .'.'. $file->getClientOriginalExtension();
          $file->storeAs('industrialSecure/documents/files/', $nameFile,'s3');
          $document->file = $nameFile;
        }
        
        $document->name = $request->name;
        
        if(!$document->save()) {
          return $this->respondHttp500();
        }

        if ($request->get('users_id') && COUNT($request->get('users_id')) > 0)
        {
          $document->users()->sync($this->getDataFromMultiselect($request->get('users_id')));
        }

        if ($request->get('roles_id') && COUNT($request->get('roles_id')) > 0)
        {
          $document->roles()->sync($this->getDataFromMultiselect($request->get('roles_id')));
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
     * @param  \App\Document  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
      try
      {
        if ($document->user_creator_id != $this->user->id)
        {
          return $this->respondWithError('No tiene permitido eliminar este archivo');
        }

        Storage::disk('s3')->delete('industrialSecure/documents/files/'. $document->file);
        
        if(!$document->delete())
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
    public function download(Document $document)
    {
      $name = $document->name;

      if ($name)
          return Storage::disk('s3')->download('industrialSecure/documents/files/'. $document->file, $name);
      else
          return Storage::disk('s3')->download('industrialSecure/documents/files/'. $document->file);
    }
}
