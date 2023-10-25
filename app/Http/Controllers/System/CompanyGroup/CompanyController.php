<?php

namespace App\Http\Controllers\System\CompanyGroup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\CompanyGroup;
use App\Models\General\Team;
use App\Http\Requests\System\Companies\CompanyGroupRequest;
use DB;
use App\Jobs\System\Companies\SyncUsersSuperadminJob;

class CompanyController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:companies_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:companies_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:companies_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:companies_d, {$this->team}", ['only' => 'destroy']);
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
        $companies = CompanyGroup::select('*')
        ->orderBy('sau_company_groups.id', 'DESC');

        return Vuetable::of($companies)
            ->addColumn('switchStatus', function ($company) {
                return true; 
            })
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CompanyGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyGroupRequest $request)
    {
        DB::beginTransaction();
        
        try
        {
            if ($request->get('emails') && count($request->get('emails')) > 0)
                $mails = $this->getDataFromMultiselect($request->get('emails'));
            else
                $mails = NULL;

            $companyGroup = new CompanyGroup;
            $companyGroup->name = $request->name;
            $companyGroup->receive_report = $request->receive_report;
            $companyGroup->emails = $request->get('emails') ? implode($mails, ',') : $mails;
        
            if (!$companyGroup->save())
                return $this->respondHttp500();

            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el grupo'
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
            $company = CompanyGroup::findOrFail($id);

            return $this->respondHttp200([
                'data' => $company,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CompanyGroupRequest  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyGroupRequest $request, CompanyGroup $companyGroup)
    {
        DB::beginTransaction();

        try
        {
            if ($request->get('emails') && count($request->get('emails')) > 0)
                $mails = $this->getDataFromMultiselect($request->get('emails'));
            else
                $mails = NULL;

            $companyGroup->fill($request->all());
            $companyGroup->emails = $request->get('emails') ? implode($mails, ',') : $mails;
           
            if(!$companyGroup->update())
                return $this->respondHttp500();
                
            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el grupo de compañia'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $companyGroup = CompanyGroup::findOrFail($id);
        
        if(!$companyGroup->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el grupo de compañias'
        ]);
    }

    /**
     * toggles the check state between open and close
     * @param  Company $company
     * @return \Illuminate\Http\Response
     */
    public function toggleState(CompanyGroup $company)
    {
        try
        {
            $newState = $company->isActive() ? "NO" : "SI";
            $data = ['active' => $newState];

            if (!$company->update($data)) {
                return $this->respondHttp500();
            }
            
            return $this->respondHttp200([
                'message' => 'Se cambio el estado del grupo'
            ]);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }
}
