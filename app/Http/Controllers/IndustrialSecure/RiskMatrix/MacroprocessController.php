<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Processes\TagsProcess;

class MacroprocessController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
       /* $this->middleware("permission:regionals_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:regionals_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:regionals_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:regionals_d, {$this->team}", ['only' => 'destroy']);*/
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
        $macroprocess = TagsProcess::select('*');

        return Vuetable::of($macroprocess)
                    ->make();
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
            $macroprocess = TagsProcess::findOrFail($id);

            return $this->respondHttp200([
                'data' => $macroprocess,
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
    public function update(Request $request, TagsProcess $macroprocess)
    {
        $macroprocess->fill($request->all());
        
        if(!$macroprocess->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }
}
