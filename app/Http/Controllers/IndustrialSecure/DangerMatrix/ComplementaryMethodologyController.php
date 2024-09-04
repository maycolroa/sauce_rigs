<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Traits\Filtertrait;
use Illuminate\Database\Eloquent\Collection;
use App\Models\IndustrialSecure\DangerMatrix\ComplementaryMethodology;
use App\Models\IndustrialSecure\DangerMatrix\ComplementaryMethodologyLogHistories;
use App\Http\Requests\IndustrialSecure\Documents\DocumentRequest;
use DB;
use Validator;

class ComplementaryMethodologyController extends Controller
{
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:dangerMatrix_methodology_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:dangerMatrix_methodology_r, {$this->team}");
        $this->middleware("permission:dangerMatrix_methodology_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:dangerMatrix_methodology_d, {$this->team}", ['only' => 'destroy']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $files = ComplementaryMethodology::selectRaw(
            'sau_dm_complementary_methodologies.name,
             sau_dm_complementary_methodologies.created_at,
             sau_dm_complementary_methodologies.id,
             sau_dm_complementary_methodologies.type,
             sau_dm_complementary_methodologies.observations,
             sau_users.name as user'
          )
          ->join('sau_users','sau_users.id','sau_dm_complementary_methodologies.user_id')
          ->orderBy('id', 'DESC');
        
        return Vuetable::of($files)
            ->make();
    }

    public function history(Request $request)
    {
        $files = ComplementaryMethodologyLogHistories::selectRaw(
            'sau_dm_complementary_methodology_log_histories.created_at,
             sau_dm_complementary_methodology_log_histories.id,
             sau_dm_complementary_methodology_log_histories.name_old,
             sau_dm_complementary_methodology_log_histories.type_old,
             sau_dm_complementary_methodology_log_histories.observations_old,
             sau_dm_complementary_methodology_log_histories.name_new,
             sau_dm_complementary_methodology_log_histories.type_new,
             sau_dm_complementary_methodology_log_histories.observations_new,
             sau_users.name as user'
          )
          ->join('sau_users','sau_users.id','sau_dm_complementary_methodology_log_histories.user_id')
          ->where('sau_dm_complementary_methodology_log_histories.methodology_id', $request->metodologyId)
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
      Validator::make($request->all(), [
        "file" => [
            function ($attribute, $value, $fail)
            {
                if ($value && !is_string($value) && $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                $value->getClientMimeType() != 'application/zip' && $value->getClientMimeType() != 'application/x-zip-compressed' )
                    $fail('Archivo debe ser un xlsx o un zip');
            },
          ],
          "observations" => 'required'
      ])->validate();

      DB::beginTransaction();

      try
      {
        $complementaryMethodology = new ComplementaryMethodology();
        $file = $request->file;
        $nameFile = base64_encode($this->user->id . now()) .'.'. pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        $file->storeAs('industrialSecure/complementaryMethodology/files/', $nameFile,'s3');

        $complementaryMethodology->file = $nameFile;
        $complementaryMethodology->user_id = $this->user->id;
        $complementaryMethodology->name = $request->name;
        $complementaryMethodology->type = $request->type;
        $complementaryMethodology->observations = $request->observations;
        $complementaryMethodology->company_id = $this->company;
      
        if(!$complementaryMethodology->save())
        {
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Metodologias complementarias de valoracion de riesgos', 'Se creo la metodologia llamada '.$complementaryMethodology->name);

        DB::commit();

      }
      catch(\Exception $e) {
        DB::rollback();
        \Log::info($e->getMessage());
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se cargo la metodologia correctamente'
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
        $complementaryMethodology = ComplementaryMethodology::findOrFail($id);

        return $this->respondHttp200([
            'data' => $complementaryMethodology,
        ]);

      } catch(Exception $e) {
        \Log::info($e->getMessage());
        $this->respondHttp500();
      }
    }

    public function saveLogHistory($new, $old)
    {
        $record = new ComplementaryMethodologyLogHistories;
        $record->user_id = $this->user->id;
        $record->methodology_id = $new->id;
        $record->name_old = $old->name;
        $record->name_new = $new->name;
        $record->type_old = $old->type;
        $record->type_new = $new->type;
        $record->observations_old = $old->observations;
        $record->observations_new = $new->observations;
        $record->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentRequest $request, ComplementaryMethodology $complementaryMethodology)
    {
      Validator::make($request->all(), [
          "observations" => 'required'
      ])->validate();


      DB::beginTransaction();

      try
      {
        $beforeFile= $complementaryMethodology;


        if (($complementaryMethodology->name != $request->name) || ($complementaryMethodology->type != $request->type) || ($complementaryMethodology->observations != $request->observations))
        {
          $this->saveLogHistory($request, $complementaryMethodology);
        }

        $complementaryMethodology->name = $request->name;
        $complementaryMethodology->type = $request->type;
        $complementaryMethodology->observations = $request->observations;
        
        if(!$complementaryMethodology->save()) {
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Metodologias complementarias de valoracion de riesgos', 'Se edito la metodologia llamada '.$complementaryMethodology->name);

        DB::commit();

      }
      catch(\Exception $e) {
        DB::rollback();
        \Log::info($e->getMessage());
        return $this->respondHttp500();
      }

      return $this->respondHttp200([
        'message' => 'Se actualizo la metodologia correctamente'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComplementaryMethodology $complementaryMethodology)
    {
      try
      {
        Storage::disk('s3')->delete('industrialSecure/complementaryMethodology/files/'. $complementaryMethodology->file);


        $this->saveLogActivitySystem('Metodologias complementarias de valoracion de riesgos', 'Se elimino la metodologia llamada: '.$complementaryMethodology->name . ', del tipo: '.$complementaryMethodology->type. 'y con las observaciones: '. $complementaryMethodology->observations);
        
        if(!$complementaryMethodology->delete())
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
    public function download(ComplementaryMethodology $complementaryMethodology)
    {
      $name = $complementaryMethodology->name;
      $extencion = explode('.', $complementaryMethodology->file);
      $nombre = $name.'.'.$extencion[1];

      return Storage::disk('s3')->download('industrialSecure/complementaryMethodology/files/'. $complementaryMethodology->file, $nombre);
    }
}
