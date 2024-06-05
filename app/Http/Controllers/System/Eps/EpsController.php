<?php

namespace App\Http\Controllers\System\Eps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Employees\EmployeeEPS;
use DB;

class EpsController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:labels_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:labels_u, {$this->team}", ['only' => 'update']);*/
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
        $eps = EmployeeEPS::selectRaw(
            "sau_employees_eps.*,
            if(sau_employees_eps.state, 'SI', 'NO') AS state"
        )
        ->orderBy('sau_employees_eps.id', 'DESC');

        return Vuetable::of($eps)
                    ->make();
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\NewsletterSendRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try
        {
            $eps = new EmployeeEPS;
            $eps->name = $request->name;
            $eps->code = $request->code;
        
            if (!$eps->save())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la EPS'
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
            $eps = EmployeeEPS::findOrFail($id);

            return $this->respondHttp200([
                'data' => $eps,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\System\Labels\LabelRequest  $request
     * @param  Keyword  $label
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeEPS $employeeEps)
    {
        DB::beginTransaction();

        try
        {
            $employeeEps = EmployeeEPS::findOrFail($request->id);
            $employeeEps->fill($request->all());
            
            if (!$employeeEps->update())
                return $this->respondHttp500();
            
            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se actualizo la EPS'
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            DB::rollback();
            $this->respondHttp500();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eps = EmployeeEPS::findOrFail($id);
        
        if(!$eps->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la EPS'
        ]);
    }

    public function toggleState(EmployeeEPS $eps)
    {
        $newState = !$eps->state;
        $data = ['state' => $newState];

        if (!$eps->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado de la EPS'
        ]);
    }
}
