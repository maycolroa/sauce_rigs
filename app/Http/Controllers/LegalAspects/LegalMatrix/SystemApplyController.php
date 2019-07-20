<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\SystemApply;
use App\Http\Requests\LegalAspects\LegalMatrix\SystemApplyRequest;
use Session;

class SystemApplyController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:systemApply_c|systemApplyCustom_c', ['only' => 'store']);
        $this->middleware('permission:systemApply_r|systemApplyCustom_r', ['except' => ['multiselect', 'multiselectSystem', 'multiselectCompany']]);
        $this->middleware('permission:systemApply_u|systemApplyCustom_u', ['only' => 'update']);
        $this->middleware('permission:systemApply_d|systemApplyCustom_d', ['only' => 'destroy']);
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
        if ($request->has('custom'))
            $system_apply = SystemApply::company()->select('*');
        else
            $system_apply = SystemApply::system()->select('*');

        return Vuetable::of($system_apply)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\LegalMatrix\SystemApplyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SystemApplyRequest $request)
    {
        $system_apply = new SystemApply($request->all());

        if ($request->custom == 'true')
            $system_apply->company_id = Session::get('company_id');
        
        if(!$system_apply->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el Sistema que Aplica'
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
            $system_apply = SystemApply::findOrFail($id);
            $system_apply->custom = $system_apply->company_id ? true : false;   

            return $this->respondHttp200([
                'data' => $system_apply,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\LegalMatrix\SystemApplyRequest $request
     * @param  SystemApply $systemApply
     * @return \Illuminate\Http\Response
     */
    public function update(SystemApplyRequest $request, SystemApply $systemApply)
    {
        $systemApply->fill($request->all());
        
        if(!$systemApply->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el Sistema que Aplica'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SystemApply $systemApply
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemApply $systemApply)
    {
        if (COUNT($systemApply->laws) > 0)
        {
            return $this->respondWithError('No se puede eliminar el Sistema que Aplica porque hay registros asociados a él');
        }

        if(!$systemApply->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el Sistema que Aplica'
        ]);
    }

    public function multiselectSystem(Request $request)
    {
        return $this->multiselect($request, 'system');
    }

    public function multiselectCompany(Request $request)
    {
        return $this->multiselect($request, 'company');
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request, $scope = 'all')
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $system_apply = SystemApply::select("id", "name")
                ->$scope()
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($system_apply)
            ]);
        }
        else
        {
            $system_apply = SystemApply::select(
                'sau_lm_system_apply.id as id',
                'sau_lm_system_apply.name as name'
            )
            ->$scope()
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($system_apply);
        }
    }
}
