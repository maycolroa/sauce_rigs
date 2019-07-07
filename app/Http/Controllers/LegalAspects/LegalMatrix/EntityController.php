<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Entity;
use App\Http\Requests\LegalAspects\LegalMatrix\EntityRequest;
use Session;

class EntityController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('permission:activities_c', ['only' => 'store']);
        $this->middleware('permission:activities_r', ['except' =>'multiselect']);
        $this->middleware('permission:activities_u', ['only' => 'update']);
        $this->middleware('permission:activities_d', ['only' => 'destroy']);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $entities = Entity::select('*');

        return Vuetable::of($entities)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntityRequest $request)
    {
        $entity = new Entity($request->all());
        
        if(!$entity->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la entidad'
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
        try
        {
            $entity = Entity::findOrFail($id);

            return $this->respondHttp200([
                'data' => $entity,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(EntityRequest $request, Entity $entity)
    {
        $entity->fill($request->all());
        
        if(!$entity->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la entidad'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entity $entity)
    {
        if (COUNT($entity->laws) > 0)
        {
            return $this->respondWithError('No se puede eliminar la entidad porque hay registros asociados a ella');
        }

        if(!$entity->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la entidad'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $entities = Entity::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($entities)
            ]);
        }
        else
        {
            $entities = Entity::select(
                'sau_lm_entities.id as id',
                'sau_lm_entities.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($entities);
        }
    }
}
