<?php

namespace App\Http\Controllers\Administrative\Regionals;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Http\Requests\Administrative\Regionals\RegionalRequest;
use App\Exports\Administrative\Regionals\RegionalImportTemplateExcel;
use App\Jobs\Administrative\Regionals\RegionalImportJob;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeRegionalController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:regionals_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:regionals_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:regionals_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:regionals_d, {$this->team}", ['only' => 'destroy']);
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
        $regionals = EmployeeRegional::select('*')->orderBy('id', 'DESC');

        return Vuetable::of($regionals)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionalRequest $request)
    {
        $regional = new EmployeeRegional($request->all());
        $regional->company_id = $this->company;
        
        if(!$regional->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Regionales', 'Se creo la regional  '.$regional->name);

        return $this->respondHttp200([
            'message' => 'Se creo el registro'
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
            $regional = EmployeeRegional::findOrFail($id);

            return $this->respondHttp200([
                'data' => $regional,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function update(RegionalRequest $request, EmployeeRegional $regional)
    {
        $regional->fill($request->all());
        
        if(!$regional->update()){
          return $this->respondHttp500();
        }
        
        $this->saveLogActivitySystem('Regionales', 'Se edito la regional  '.$regional->name);

        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeRegional $regional)
    {
        if (count($regional->employees) > 0 || count($regional->headquarters) > 0 || count($regional->dangerMatrices) > 0 || count($regional->reports) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Regionales', 'Se elimino la regional  '.$regional->name);

        if(!$regional->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el registro'
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
            $regionals = EmployeeRegional::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)
                ->get();

            if ($request->has('form') && $request->form == 'inspections') 
                $regionals->push(['id' => 'Todos', 'name' => 'Todos']);
                
            $regionals = $regionals->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($regionals)
            ]);
        }
        else
        {
            $regionals = EmployeeRegional::selectRaw("
                sau_employees_regionals.id as id,
                sau_employees_regionals.name as name
            ")->orderBy('name')->pluck('id', 'name');
        
            return $this->multiSelectFormat($regionals);
        }
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new RegionalImportTemplateExcel(collect([]), $this->company), 'PlantillaImportacionNiveles.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        RegionalImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }
}
