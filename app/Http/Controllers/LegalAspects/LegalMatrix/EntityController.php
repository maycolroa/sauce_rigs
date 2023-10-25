<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Entity;
use App\Http\Requests\LegalAspects\LegalMatrix\EntityRequest;

class EntityController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:entities_c|entitiesCustom_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:entities_r|entitiesCustom_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:entities_u|entitiesCustom_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:entities_d|entitiesCustom_d, {$this->team}", ['only' => 'destroy']);
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
        if ($request->has('custom'))
            $entities = Entity::company()->select('*')->orderBy('id', 'DESC');
        else
            $entities = Entity::system()->select('*')->orderBy('id', 'DESC');

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

        if ($request->custom == 'true')
            $entity->company_id = $this->company;
        
        if(!$entity->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz legal - Entidades', 'Se creo la entidad '.$entity->name);

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
            $entity->custom = $entity->company_id ? true : false;

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

        $this->saveLogActivitySystem('Matriz legal - Entidades', 'Se edito la entidad '.$entity->name);
        
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

        $this->saveLogActivitySystem('Matriz legal - Entidades', 'Se elimino la entidad '.$entity->name);

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

    public function multiselect(Request $request, $scope = 'alls')
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $entities = Entity::select("id", "name")
                ->$scope()
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($entities)
            ]);
        }
        else
        {
            $entities = Entity::selectRaw(
                "GROUP_CONCAT(sau_lm_entities.id) as ids,
                 sau_lm_entities.name as name")
            ->$scope()
            ->orderBy('name')
            ->groupBy('sau_lm_entities.name')
            ->pluck('ids', 'name');
        
            return $this->multiSelectFormat($entities);
        }
    }

    public function multiselectSystem(Request $request)
    {
        return $this->multiselect($request, 'system');
    }

    public function multiselectCompany(Request $request)
    {
        return $this->multiselect($request, 'company');
    }
}
