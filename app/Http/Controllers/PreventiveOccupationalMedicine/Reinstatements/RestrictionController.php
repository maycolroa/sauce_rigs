<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Restriction;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\RestrictionRequest;

class RestrictionController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_restrictions_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:reinc_restrictions_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:reinc_restrictions_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:reinc_restrictions_d, {$this->team}", ['only' => 'destroy']);
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
        $restrictions = Restriction::select('*');

        return Vuetable::of($restrictions)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\RestrictionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RestrictionRequest $request)
    {
        $restriction = new Restriction($request->all());
        $restriction->company_id = $this->company;
        
        if(!$restriction->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Restricciones', 'Se creo la restriccion '. $restriction->name);

        return $this->respondHttp200([
            'message' => 'Se creo la Restricción'
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
            $restriction = Restriction::findOrFail($id);

            return $this->respondHttp200([
                'data' => $restriction,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\RestrictionRequest  $request
     * @param  Restriction  $restriction
     * @return \Illuminate\Http\Response
     */
    public function update(RestrictionRequest $request, Restriction $restriction)
    {
        $restriction->fill($request->all());
        
        if(!$restriction->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Restricciones', 'Se edito la restriccion '. $restriction->name);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la Restricción'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Restriction  $restriction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restriction $restriction)
    {
        //Falta probar esto
        if (COUNT($restriction->checks) > 0)
            return $this->respondWithError('No se puede eliminar la Restricción porque hay registros asociados a él');

        $this->saveLogActivitySystem('Reincorporaciones - Restricciones', 'Se elimino la restriccion '. $restriction->name);

        if (!$restriction->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la Restricción'
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
            $restrictions = Restriction::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($restrictions)
            ]);
        }
        else
        {
            $restrictions = Restriction::select(
                'sau_reinc_restrictions.id as id',
                'sau_reinc_restrictions.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($restrictions);
        }
    }
}
