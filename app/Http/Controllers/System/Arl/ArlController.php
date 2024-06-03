<?php

namespace App\Http\Controllers\System\Arl;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Employees\EmployeeARL;
use DB;

class ArlController extends Controller
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
        $arl = EmployeeARL::select('*')
        ->orderBy('sau_employees_arl.id', 'DESC');

        return Vuetable::of($arl)
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
            $arl = new EmployeeARL;
            $arl->name = $request->name;
            $arl->code = $request->code;
        
            if (!$arl->save())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la ARL'
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
            $arl = EmployeeARL::findOrFail($id);

            return $this->respondHttp200([
                'data' => $arl,
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
    public function update(Request $request, EmployeeARL $employeeArl)
    {
        DB::beginTransaction();

        try
        {
            $arl = EmployeeARL::findOrFail($employeeArl->id);
            $arl->name = $request->name;
            $arl->code = $request->code;
            
            if (!$arl->update())
                return $this->respondHttp500();
            
            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se actualizo la ARL'
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
        $arl = EmployeeARL::findOrFail($id);
        
        if(!$arl->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la ARL'
        ]);
    }
}
