<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\ProyectContract;
use App\Http\Requests\LegalAspects\Contracts\ProyectContractRequest;
use Carbon\Carbon;
use DB;

class ContractProyectController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_proyects_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_proyects_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:contracts_proyects_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_proyects_d, {$this->team}", ['only' => 'destroy']);
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
        $proyects = ProyectContract::select('*')->where('company_id', $this->company)->orderBy('id', 'DESC');

        return Vuetable::of($proyects)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ProyectContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProyectContractRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $proyect = new ProyectContract;
            $proyect->company_id = $this->company;
            $proyect->name = $request->name;

            if (!$proyect->save())
                return $this->respondHttp500();

            DB::commit();

        $this->saveLogActivitySystem('Contratistas - Proyectos', 'Se creo el proyecto '.$proyect->name);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el proyecto'
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
            $proyect = ProyectContract::findOrFail($id);

            return $this->respondHttp200([
                'data' => $proyect,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ProyectContractRequest  $request
     * @param  Activity  $proyect
     * @return \Illuminate\Http\Response
     */
    public function update(ProyectContractRequest $request, ProyectContract $proyectContract)
    {
        DB::beginTransaction();

        try
        {
            $proyectContract->fill($request->all());

            if(!$proyectContract->update()){
                return $this->respondHttp500();
            }

            DB::commit();

            $this->saveLogActivitySystem('Contratistas - Proyectos', 'Se edito el proyecto '.$proyectContract->name);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el proyecto'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $proyect
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProyectContract $proyectContract)
    {
        $this->saveLogActivitySystem('Contratistas - Proyectos', 'Se elimino el proyecto '.$proyectContract->name);

        if (!$proyectContract->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el proyecto'
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
            $proyects = ProyectContract::select("id", "name")
                ->where('company_id', $this->company)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($proyects)
            ]);
        }
        else
        {
            $proyects = ProyectContract::selectRaw("
                sau_ct_proyects.id as id,
                sau_ct_proyects.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($proyects);
        }
    }
}
