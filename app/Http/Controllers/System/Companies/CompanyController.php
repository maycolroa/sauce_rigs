<?php

namespace App\Http\Controllers\System\Companies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Company;
use App\Models\General\WorkCenter;
use App\Models\General\Team;
use App\Http\Requests\System\Companies\CompanyRequest;
use DB;
use App\Jobs\System\Companies\SyncUsersSuperadminJob;
use App\Traits\Filtertrait;
use Carbon\Carbon;

class CompanyController extends Controller
{
    use Filtertrait;
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
        $companies = Company::select(
            'sau_companies.*',
            'sau_company_groups.name as group'
        )
        ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id');

        $url = "/system/companies";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["groups"]) && $filters["groups"])
                $companies->inGroups($this->getValuesForMultiselect($filters["groups"]), $filters['filtersType']['groups']);
        }

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
            $company->users = [];
            $company->delete = [];

            $company->multiselect_company_group = $company->company_group_id ? $company->group->multiselect() : [];
            $company->multiselect_departament_sede = $company->departamento_sede_principal_id ? $company->departament->multiselect() : [];
            $company->multiselect_municipality_sede = $company->ciudad_sede_principal_id ? $company->city->multiselect() : [];
            $company->work_centers = [];

            foreach ($company->centers as $key => $center)
            {   
                $center->key = (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20);

                $center->multiselect_departament_centro = $center->departament_id ? $center->departament->multiselect() : [];
                $center->multiselect_municipality_centro = $center->city_id ?$center->city->multiselect() : [];
            }

            $company->group_company = $company->company_group_id ? 'SI' : 'NO';

            $company->work_centers = $company->centers;

            return $this->respondHttp200([
                'data' => $company,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
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

            if ($request->group_company == 'NO')
                $company->company_group_id = NULL;

            if ($request->ph_state_incentives == true)
                $company->ph_state_incentives = 1;
            else
                $company->ph_state_incentives = 0;
           
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

            if ($request->has('users') && COUNT($request->users) > 0)
            {
                foreach ($request->users as $key => $value)
                {
                    $user = User::find($value['user_id']);

                    if ($user)
                    {
                        $user->companies()->attach($company->id);

                        $roles = $this->getValuesForMultiselect($value['role_id']);
                        $roles = Role::whereIn('id', $roles)->get();

                        if (COUNT($roles) > 0)
                        {
                            $team = Team::where('name', $company->id)->first();
                            $user->attachRoles($roles, $team);
                        }
                    }
                }
            }

            if ($request->has('work_centers') && COUNT($request->work_centers) > 0)
            {
                foreach ($request->work_centers as $key => $value)
                {
                    $center = WorkCenter::updateOrCreate(
                        [
                            'activity_economic' => $value['activity_economic'],
                            'departament_id' => $value['departament_id'],
                            'city_id' => $value['city_id'],
                            'company_id' => $this->company
                        ],
                        [
                            'activity_economic' => $value['activity_economic'],
                            'direction' => $value['direction'],
                            'telephone' => $value['telephone'],
                            'email' => $value['email'],
                            'departament_id' => $value['departament_id'],
                            'city_id' => $value['city_id'],
                            'zona' => $value['zona'],
                            'company_id' => $this->company
                        ]
                    );

                }
            }
                
            DB::commit();

            if (!$request->info)
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

    public function multiselectUsers(Request $request)
    {
        $users_ids = User::withoutGlobalScopes()
                ->select('sau_users.*')
                ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->where('sau_company_user.company_id', $request->id)
                ->pluck('id')
                ->toArray();

        $users = User::select(
                    "sau_users.id AS id",
                    DB::raw("CONCAT(sau_users.document, ' - ', sau_users.name) AS name")
                )
                ->active()
                ->whereNotIn('sau_users.id', $users_ids)
                ->pluck('id', 'name');
                
        return $this->multiSelectFormat($users);
    }

    public function multiselectRoles(Request $request)
    {
        $roles = Role::form(false, $request->id)
                    ->select("id", "name")
                    ->pluck('id', 'name');

        return $this->multiSelectFormat($roles);
    }

    public function multiselectCenters(Request $request)
    {
        $centers = WorkCenter::select("id", "activity_economic as name")
                    ->pluck('id', 'name');

        return $this->multiSelectFormat($centers);
    }
}
