<?php

namespace App\Http\Controllers\System\Companies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\General\Company;
use App\Models\General\Team;
use App\Http\Requests\System\Companies\CompanyRequest;
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
        $companies = Company::select('*');

        return Vuetable::of($companies)
            ->addColumn('switchStatus', function ($company) {
                return $company->id != $this->company; 
            })
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CompanyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        DB::beginTransaction();
        
        try
        {
            $company = new Company($request->all());
        
            if (!$company->save())
                return $this->respondHttp500();

            $team = new Team();
            $team->id = $company->id;
            $team->name = $company->id;
            $team->display_name = $company->name;
            $team->description = "Equipo ".$company->name;
            $team->save();

            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            //\Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la compañia'
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
            $company = Company::findOrFail($id);

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
     * @param  App\Http\Requests\IndustrialSecure\Activities\CompanyRequest  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        DB::beginTransaction();

        try
        {
            $company->fill($request->all());
            
            if(!$company->update())
                return $this->respondHttp500();

            $team = Team::updateOrCreate(
                [
                    'name' => $company->id,
                ], 
                [
                    'id' => $company->id,
                    'name' => $company->id,
                    'display_name' => $company->name,
                    'description' => "Equipo ".$company->name
                ]
            );
                
            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la compañia'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        
    }

    /**
     * toggles the check state between open and close
     * @param  Company $company
     * @return \Illuminate\Http\Response
     */
    public function toggleState(Company $company)
    {
        $newState = $company->isActive() ? "NO" : "SI";
        $data = ['active' => $newState];

        if (!$company->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado de la compañia'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    /*public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $activities = Activity::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = Activity::selectRaw("
                sau_dm_activities.id as id,
                sau_dm_activities.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }*/
}
