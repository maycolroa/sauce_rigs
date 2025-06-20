<?php

namespace App\Http\Controllers\IndustrialSecure\Dangers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Dangers\Danger;
use App\Http\Requests\IndustrialSecure\Dangers\DangerRequest;

class DangerController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:dangers_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:dangers_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:dangers_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:dangers_d, {$this->team}", ['only' => 'destroy']);
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
        $dangers = Danger::select('*')
        ->orderBy('id', 'DESC');

        return Vuetable::of($dangers)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Dangers\DangerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DangerRequest $request)
    {
        $danger = new Danger($request->all());
        $danger->company_id = $this->company;
        
        if(!$danger->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - Peligros', 'Se creo el peligro '.$danger->name.' ');

        return $this->respondHttp200([
            'message' => 'Se creo el peligro'
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
            $danger = Danger::findOrFail($id);

            return $this->respondHttp200([
                'data' => $danger,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Dangers\DangerRequest  $request
     * @param  Danger  $danger
     * @return \Illuminate\Http\Response
     */
    public function update(DangerRequest $request, Danger $danger)
    {
        $danger->fill($request->all());
        
        if(!$danger->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - Actividades', 'Se edito el peligro '.$danger->name.' ');

        
        return $this->respondHttp200([
            'message' => 'Se actualizo el peligro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Danger  $danger
     * @return \Illuminate\Http\Response
     */
    public function destroy(Danger $danger)
    {
        if (count($danger->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar el peligro porque hay matrices de peligro asociadas a el');
        }

        if(!$danger->delete())
        {
            return $this->respondHttp500();
        }


        $this->saveLogActivitySystem('Matriz de peligros - Actividades', 'Se elimino el peligro '.$danger->name.' ');

        
        return $this->respondHttp200([
            'message' => 'Se elimino el peligro'
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
            $dangers = Danger::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($dangers)
            ]);
        }
        else
        {
            $dangers = Danger::selectRaw("
                sau_dm_dangers.id as id,
                sau_dm_dangers.name as name
            ")
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($dangers);
        }
    }
}
