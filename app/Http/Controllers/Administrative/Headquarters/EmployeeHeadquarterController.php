<?php

namespace App\Http\Controllers\Administrative\Headquarters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Http\Requests\Administrative\Headquarters\HeadquarterRequest;

class EmployeeHeadquarterController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:headquarters_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:headquarters_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:headquarters_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:headquarters_d, {$this->team}", ['only' => 'destroy']);
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
        $headquarters = EmployeeHeadquarter::select(
            'sau_employees_headquarters.id as id',
            'sau_employees_headquarters.name as name',
            'sau_employees_regionals.name as regional'
        )->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id');

        return Vuetable::of($headquarters)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\HeadquarterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HeadquarterRequest $request)
    {
        $headquarter = new EmployeeHeadquarter($request->all());
        
        if(!$headquarter->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Sedes', 'Se creo la sede  '.$headquarter->name);

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
            $headquarter = EmployeeHeadquarter::findOrFail($id);

            $headquarter->multiselect_regional = $headquarter->regional->multiselect(); 

            return $this->respondHttp200([
                'data' => $headquarter,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\HeadquarterRequest  $request
     * @param  EmployeeHeadquarter  $headquarter
     * @return \Illuminate\Http\Response
     */
    public function update(HeadquarterRequest $request, EmployeeHeadquarter $headquarter)
    {
        $headquarter->fill($request->all());
        
        if(!$headquarter->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Sedes', 'Se edito la sede  '.$headquarter->name);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeHeadquarter $headquarter
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeHeadquarter $headquarter)
    {
        if (count($headquarter->employees) > 0 || count($headquarter->processes) > 0 || count($headquarter->dangerMatrices) > 0 || count($headquarter->reports) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Sedes', 'Se elimino la sede  '.$headquarter->name);

        if(!$headquarter->delete())
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
        $regionals = EmployeeRegional::selectRaw(
            "sau_employees_regionals.id as id"
        )
        ->pluck('id')
        ->toArray();

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $headquarters = EmployeeHeadquarter::selectRaw(
                "sau_employees_headquarters.id as id,
                sau_employees_headquarters.name as name")
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
            ->where(function ($query) use ($keyword) {
                $query->orWhere('sau_employees_headquarters.name', 'like', $keyword);
            });

            if ($request->has('regional') && $request->get('regional') != '')
            {
                $regional = $request->get('regional');
                
                if (is_numeric($regional))
                    $headquarters->where('employee_regional_id', $request->get('regional'));
                else
                {
                    $regionals_select = $this->getValuesForMultiselect($regional);

                    if ($request->has('form') && $request->form == 'inspections')
                    {
                        if (in_array('Todos', $regionals_select->toArray()))
                            $headquarters->whereIn('employee_regional_id', $regionals);
                        else
                            $headquarters->whereIn('employee_regional_id', $regionals_select);
                    }
                    else
                        $headquarters->whereIn('employee_regional_id', $regionals_select);
                }
            }

            $headquarters = $headquarters->orderBy('name')->take(30)->get();

            if ($request->has('form') && $request->form == 'inspections' && $headquarters->count() > 0)
                $headquarters->push(['id' => 'Todos', 'name' => 'Todos']);
                
            $headquarters = $headquarters->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($headquarters)
            ]);
        }
        else
        {
            $headquarters = EmployeeHeadquarter::selectRaw(
                "GROUP_CONCAT(sau_employees_headquarters.id) as ids,
                 sau_employees_headquarters.name as name")
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
            ->groupBy('sau_employees_headquarters.name')
            ->orderBy('name')
            ->pluck('ids', 'name');
        
            return $this->multiSelectFormat($headquarters);
        }
    }
}
