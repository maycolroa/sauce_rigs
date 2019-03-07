<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeeHeadquarter;
use App\Http\Requests\Administrative\Headquarters\HeadquarterRequest;
use Session;

class EmployeeHeadquarterController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:headquarters_c', ['only' => 'store']);
        $this->middleware('permission:headquarters_r', ['except' =>'multiselect']);
        $this->middleware('permission:headquarters_u', ['only' => 'update']);
        $this->middleware('permission:headquarters_d', ['only' => 'destroy']);
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

        return $this->respondHttp200([
            'message' => 'Se creo la sede'
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
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la sede'
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
        if (count($headquarter->employees) > 0 || count($headquarter->processes) > 0 || count($headquarter->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar la sede porque hay registros asociadas a ella');
        }

        if(!$headquarter->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la sede'
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
            if ($request->has('regional') && $request->get('regional') != '')
            {
                $keyword = "%{$request->keyword}%";
                $headquarters = EmployeeHeadquarter::selectRaw(
                    "sau_employees_headquarters.id as id,
                    sau_employees_headquarters.name as name")
                ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
                ->where('employee_regional_id', $request->get('regional'))
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_employees_headquarters.name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($headquarters)
                ]);
            }
        }
        else
        {
            $headquarters = EmployeeHeadquarter::selectRaw(
                "sau_employees_headquarters.id as id,
                CONCAT(sau_employees_regionals.name, '/', sau_employees_headquarters.name) as name")
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')->pluck('id', 'name');
        
            return $this->multiSelectFormat($headquarters);
        }
    }
}
