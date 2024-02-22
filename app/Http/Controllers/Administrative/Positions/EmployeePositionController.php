<?php

namespace App\Http\Controllers\Administrative\Positions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Http\Requests\Administrative\Positions\PositionRequest;
use App\Exports\Administrative\Positions\PositionImportTemplateExcel;
use App\Jobs\Administrative\Positions\PositionImportJob;
use App\Jobs\Administrative\Positions\PositionExportJob;
use Maatwebsite\Excel\Facades\Excel;

class EmployeePositionController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:positions_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:positions_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:positions_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:positions_d, {$this->team}", ['only' => 'destroy']);
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
       $positions = EmployeePosition::select('*')->orderBy('id', 'DESC');

       return Vuetable::of($positions)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Positions\PositionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionRequest $request)
    {
        $position = new EmployeePosition($request->all());
        $position->company_id = $this->company;
        
        if(!$position->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Cargos', 'Se creo el cargo  '.$position->name);

        if ($request->has('elements_id') && COUNT($request->get('elements_id') > 0))
            $position->elements()->sync($this->getDataFromMultiselect($request->get('elements_id')));

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
            $position = EmployeePosition::findOrFail($id);
            $elements = [];

            foreach ($position->elements as $key => $value)
            {                
                array_push($elements, $value->multiselect());
            }

            $position->multiselect_elements = $elements;
            $position->elements_id = $elements;

            return $this->respondHttp200([
                'data' => $position,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Positions\PositionRequest  $request
     * @param  EmployeePosition  $position
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, EmployeePosition $position)
    {
        $position->fill($request->all());
        
        if(!$position->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Cargos', 'Se edito el cargo  '.$position->name);

        if ($request->has('elements_id') && COUNT($request->get('elements_id') > 0))
            $position->elements()->sync($this->getDataFromMultiselect($request->get('elements_id')));
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeePosition  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeePosition $position)
    {
        if (count($position->employees) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Cargos', 'Se elimino el cargo  '.$position->name);

        if(!$position->delete())
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
            $positions = EmployeePosition::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($positions)
            ]);
        }
        else
        {
            $positions = EmployeePosition::selectRaw("
                sau_employees_positions.id as id,
                sau_employees_positions.name as name
            ")
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($positions);
        }
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new PositionImportTemplateExcel($this->company), 'PlantillaImportacionCargos.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        PositionImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }
    
    public function export()
    {
      PositionExportJob::dispatch($this->user, $this->company);
    }
}
