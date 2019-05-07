<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use App\Models\LegalAspects\Contracts\FileUpload;
use App\Http\Requests\LegalAspects\Contracts\FileUploadRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Traits\ContractTrait;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\Mail\Facades\NotificationMail;
use Session;
use DB;

class FileUploadController extends Controller
{
    use ContractTrait;
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $contract_id = null;

        $files = FileUpload::selectRaw(
            'sau_ct_file_upload_contracts_leesse.*,
             sau_users.name as user_name,
             GROUP_CONCAT(sau_ct_information_contract_lessee.social_reason ORDER BY social_reason ASC) AS social_reason,
             sau_ct_section_category_items.item_name AS item_name'
          )
          ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
          ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
          ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
          ->leftJoin('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
          ->leftJoin('sau_ct_section_category_items', 'sau_ct_section_category_items.id', 'sau_ct_file_item_contract.item_id')
          ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_section_category_items.item_name');

        $filters = $request->get('filters');

        if (isset($filters["items"]))
          $files->inItems($this->getValuesForMultiselect($filters["items"]), $filters['filtersType']['items']);

        if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
        {
          $contract_id = $this->getContractIdUser(Auth::user()->id);
          $files->where('sau_ct_information_contract_lessee.id', $contract_id);
        }
        
        return Vuetable::of($files)
            ->addColumn('legalaspects-upload-files-edit', function ($file) use ($contract_id) {
              return $this->checkPermissionUserInFile($file->user_id, $contract_id);
            })
            ->addColumn('control_delete', function ($file) use ($contract_id) {
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
      DB::beginTransaction();

      try
      {
        $fileUpload = new FileUpload();
        $file = $request->file;
        $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
        
        $file->storeAs('legalAspects/files/', $nameFile,'s3');

        $fileUpload->file = $nameFile;
        $fileUpload->user_id = Auth::user()->id;
        $fileUpload->name = $request->name;
        $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');
      
        if(!$fileUpload->save())
        {
          return $this->respondHttp500();
        }

        if ($request->get('contract_id') && COUNT($request->get('contract_id')) > 0)
        {
          $fileUpload->contracts()->sync($this->getDataFromMultiselect($request->get('contract_id')));
        }
        else 
        {
          $fileUpload->contracts()->sync([$this->getContractIdUser(Auth::user()->id)]);
        }

        DB::commit();

        $this->sendNotification($fileUpload);
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
        $fileUpload->expirationDate = $fileUpload->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$fileUpload->expirationDate))->format('D M d Y');

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
      DB::beginTransaction();

      try
      {
        $contract_id = $this->getContractIdUser(Auth::user()->id);

        if (!$this->checkPermissionUserInFile($fileUpload->user_id, $contract_id))
        {
          return $this->respondWithError('No tiene permitido editar este archivo');
        }

        if($request->file != $fileUpload->file)
        {
          $file = $request->file;
          Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);
          $nameFile = base64_encode(Auth::user()->id . now()) .'.'. $file->extension();
          $file->storeAs('legalAspects/files/', $nameFile,'s3');
          $fileUpload->file = $nameFile;
        }
        
        $fileUpload->name = $request->name;
        $fileUpload->expirationDate = $request->expirationDate == null ? null : (Carbon::createFromFormat('D M d Y', $request->expirationDate))->format('Ymd');
        
        if(!$fileUpload->save()) {
          return $this->respondHttp500();
        }

        if ($request->get('contract_id') && COUNT($request->get('contract_id')) > 0)
        {
          $fileUpload->contracts()->sync($this->getDataFromMultiselect($request->get('contract_id')));
        }
        else 
        {
          $fileUpload->contracts()->sync($this->getContractsIds());
        }

        DB::commit();

        $this->sendNotification($fileUpload, 'actualizado');
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
        $contract_id = $this->getContractIdUser(Auth::user()->id);

        if (!$this->checkPermissionUserInFile($fileUpload->user_id, $contract_id))
        {
          return $this->respondWithError('No tiene permitido eliminar este archivo');
        }

        Storage::disk('s3')->delete('legalAspects/files/'. $fileUpload->file);
        
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
      return Storage::disk('s3')->download('legalAspects/files/'. $fileUpload->file);
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
        ->send();
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFilesItem(Request $request)
    {
      $contract_id = $this->getContractIdUser(Auth::user()->id);

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
}
