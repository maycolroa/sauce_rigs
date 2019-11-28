<?php

namespace App\Http\Controllers\Administrative\Businesses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Business\EmployeeBusiness;
use App\Http\Requests\Administrative\Businesses\BusinessRequest;

class EmployeeBusinessController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:businesses_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:businesses_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:businesses_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:businesses_d, {$this->team}", ['only' => 'destroy']);
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
       $businesses = EmployeeBusiness::select('*');

       return Vuetable::of($businesses)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Businesses\BusinessRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BusinessRequest $request)
    {
        $business = new EmployeeBusiness($request->all());
        $business->company_id = $this->company;
        
        if(!$business->save()){
            return $this->respondHttp500();
        }

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
            $business = EmployeeBusiness::findOrFail($id);

            return $this->respondHttp200([
                'data' => $business,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Businesses\BusinessRequest  $request
     * @param  EmployeeBusiness  $business
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessRequest $request, EmployeeBusiness $business)
    {
        $business->fill($request->all());
        
        if(!$business->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeBusiness  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeBusiness $business)
    {
        if (count($business->employees) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        if(!$business->delete())
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
            $businesses = EmployeeBusiness::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($businesses)
            ]);
        }
        else
        {
            $businesses = EmployeeBusiness::selectRaw("
                sau_employees_businesses.id as id,
                sau_employees_businesses.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($businesses);
        }
    }
}
