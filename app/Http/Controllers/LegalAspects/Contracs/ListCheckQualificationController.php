<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\ListCheckQualification;
use App\Traits\ContractTrait;
use DB;

class ListCheckQualificationController extends Controller
{
    use ContractTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_list_standards_qualification_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_list_standards_qualification_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:contracts_list_standards_qualification_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_list_standards_qualification_d, {$this->team}", ['only' => 'destroy']);
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
        $contract = $this->getContractUser($this->user->id, $this->company);

        $qualifications = ListCheckQualification::select(
            'sau_ct_list_check_qualifications.*',
            DB::raw("case when sau_ct_list_check_qualifications.state is true then 'ACTIVA' else 'INACTIVA' end as state_list"),
            'sau_users.name as user_creator')
        ->join('sau_users', 'sau_users.id', 'sau_ct_list_check_qualifications.user_id')
        ->where('company_id', $this->company)
        ->where('contract_id', $contract->id);

        return Vuetable::of($qualifications)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contract = $this->getContractUser($this->user->id, $this->company);

        $qualification_exist = ListCheckQualification::where('company_id', $this->company)
        ->where('contract_id', $contract->id)->where('state', true)->orderBy('created_at', 'DESC')->first();

        $qualification = new ListCheckQualification($request->all());
        $qualification->company_id = $this->company;
        $qualification->contract_id = $contract->id;
        $qualification->user_id = $this->user->id;
        $qualification->state = true;
        
        if(!$qualification->save()){
            return $this->respondHttp500();
        }

        $qualification_exist->update([
            'state' => false
        ]);

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
            $qualification = ListCheckQualification::findOrFail($id);

            return $this->respondHttp200([
                'data' => $qualification,
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
    public function update(Request $request, ListCheckQualification $listCheck)
    {
        $listCheck->fill($request->all());
        
        if(!$listCheck->update()){
          return $this->respondHttp500();
        }
        
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
    public function destroy(ListCheckQualification $listCheck)
    {
        if(!$listCheck->delete())
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
                ->take(30)->pluck('id', 'name');

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
}
