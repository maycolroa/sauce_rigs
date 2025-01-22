<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalAspects\LegalMatrix\LawType;
use App\Http\Requests\LegalAspects\LegalMatrix\LawTypeRequest;
use App\Vuetable\Facades\Vuetable;

class LawTypeController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:typesCustom_c|risksAspects_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:typesCustom_r|risksAspects_r, {$this->team}", ['except' => 'multiselect']);
        $this->middleware("permission:typesCustom_u|risksAspects_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:typesCustom_d|risksAspects_d, {$this->team}", ['only' => 'destroy']);
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
            $types = LawType::company()->select('*')->orderBy('id', 'DESC');
        else
            $types = LawType::system()->select('*')->orderBy('id', 'DESC');

        return Vuetable::of($types)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LawTypeRequest $request)
    {
        $type = new LawType($request->all());

        if ($request->custom == 'true')
            $type->company_id = $this->company;

        if(!$type->save())
        {
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz legal - Tipos de normas', 'Se creo el tipo de norma '.$type->name);

        return $this->respondHttp200([
            'message' => 'Se creo el tipo'
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
            $type = LawType::findOrFail($id);
            $type->custom = $type->company_id ? true : false;

            return $this->respondHttp200([
                'data' => $type,
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
    public function update(LawTypeRequest $request, LawType $type)
    {
        $type->fill($request->all());
        
        if(!$type->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz legal - Tipos de normas', 'Se edito el tipo de norma '.$type->name);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el tipo'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(LawType $type)
    {
        if (COUNT($type->laws) > 0)
        {
            return $this->respondWithError('No se puede eliminar el tipo de norma porque hay registros asociados a ella');
        }

        $this->saveLogActivitySystem('Matriz legal - Tipos de normas', 'Se elimino el tipo de norma '.$type->name);

        if(!$type->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tipo de norma'
        ]);
    }

    public function multiselect(Request $request, $scope = 'alls')
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $lawtypes = LawType::select("id", "name")
                ->$scope()
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($lawtypes)
            ]);
        }
        else
        {
            $lawtypes = LawType::select(
                'sau_lm_laws_types.id as id',
                'sau_lm_laws_types.name as name'
            )
            ->$scope()
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($lawtypes);
        }
    }

    public function multiselectCompany(Request $request)
    {
        return $this->multiselect($request, 'company');
    }

    public function multiselectSystem(Request $request)
    {
        return $this->multiselect($request, 'system');
    }
}
