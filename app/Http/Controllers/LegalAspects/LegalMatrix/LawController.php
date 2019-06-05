<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Http\Requests\LegalAspects\LegalMatrix\LawRequest;
use Session;

class LawController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('permission:activities_c', ['only' => 'store']);
        $this->middleware('permission:activities_r', ['except' =>'multiselect']);
        $this->middleware('permission:activities_u', ['only' => 'update']);
        $this->middleware('permission:activities_d', ['only' => 'destroy']);*/
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
        $laws = Law::select('*');

        return Vuetable::of($laws)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\LegalMatrix\LawRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LawRequest $request)
    {
        $law = new Law($request->except('file'));
        
        if(!$law->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la norma'
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
            $law = Law::findOrFail($id);

            return $this->respondHttp200([
                'data' => $law,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\LawRequest $request
     * @param  Law $law
     * @return \Illuminate\Http\Response
     */
    public function update(LawRequest $request, Law $law)
    {
        $law->fill($request->all());
        
        if(!$law->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la norma'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Law $law
     * @return \Illuminate\Http\Response
     */
    public function destroy(Law $law)
    {
        if(!$law->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la norma'
        ]);
    }

    public function lmYears()
    {
        $years = [];

        for ($i = 1901; $i <= Date('Y'); $i++)
        {     
            $years[$i] = $i;            
        }

        return $this->multiSelectFormat(collect($years));
    }
}
