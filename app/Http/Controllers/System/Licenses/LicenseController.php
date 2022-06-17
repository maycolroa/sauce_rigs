<?php

namespace App\Http\Controllers\System\Licenses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\License;
use App\Models\General\ModuleDependence;
use App\Http\Requests\System\Licenses\LicenseRequest;
use App\Jobs\System\Licenses\NotifyLicenseRenewalJob;
use App\Jobs\System\Licenses\ExportLicensesJob;
use Illuminate\Validation\Rule;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use Validator;
use DB;

class LicenseController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:licenses_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:licenses_r, {$this->team}");
        $this->middleware("permission:licenses_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:licenses_d, {$this->team}", ['only' => 'destroy']);
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
        $licenses = License::system()
            ->selectRaw(
                'sau_licenses.*,
                    GROUP_CONCAT(" ", sau_modules.display_name ORDER BY sau_modules.display_name) AS modules,
                    sau_companies.name AS company'
            )
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->where('sau_modules.main', 'SI')
            ->groupBy('sau_licenses.id');

        $url = "/preventiveoccupationalmedicine/reinstatements/checks";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["modules"]) && $filters["modules"])
                $licenses->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);
                
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $licenses->betweenDate($dates);
        }

        return Vuetable::of($licenses)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\System\Licenses\LicenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LicenseRequest $request)
    {
        Validator::make($request->all(), [
            'add_email' => Rule::requiredIf(function() use($request){

                $company = Company::find($request->company_id);
                $role = Role::defined()->where('name', 'Superadmin')->first();

                $users = User::withoutGlobalScopes()->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->leftJoin('sau_role_user', function($q) use ($company) { 
                    $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                    ->on('sau_role_user.team_id', '=', DB::raw($company->id));
                })
                ->where('sau_role_user.role_id', '<>', $role->id)
                ->groupBy('sau_users.id')
                ->count();

                if ($users == 0)
                    return true;
                else
                    return false;
            })
        ],[
          'required'  => 'Por favor, agregue emails para notificar la creación de la licencia.'
        ])->validate();

        DB::beginTransaction();

        try
        {
            $license = new License($request->all());
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Ymd');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Ymd');
            
            if (!$license->save())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_main, $modules));

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);

            DB::commit();

            $mails = [];

            if ($request->has('add_email'))
                $mails = $this->getDataFromMultiselect($request->get('add_email'));

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Creación');

        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la licencia'
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
            $license = License::system()->findOrFail($id);
            $license->multiselect_company = $license->company->multiselect();
            $license->started_at = (Carbon::createFromFormat('Y-m-d', $license->started_at))->format('D M d Y');
            $license->ended_at = (Carbon::createFromFormat('Y-m-d', $license->ended_at))->format('D M d Y');

            $modules = [];

            $mails = [];

            foreach ($license->modules()->main()->get() as $key => $value)
            {               
                array_push($modules, $value->multiselect());
            }

            $license->module_id = $modules;

            $license->add_email = $mails;

            return $this->respondHttp200([
                'data' => $license,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\System\Licenses\LicenseRequest  $request
     * @param  License  $license
     * @return \Illuminate\Http\Response
     */
    public function update(LicenseRequest $request, $id)
    {
        DB::beginTransaction();

        try
        {
            $modificaciones = [];

            $license = License::system()->findOrFail($id);
            $old_company = $license->company_id;
            $old_ended = $license->ended_at;
            $old_started = $license->started_at;

            $license->fill($request->all());
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Y-m-d');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Y-m-d');

            if ($license->started_at != $old_started)
                array_push($modificaciones, ['fecha_inicio' => $license->started_at]);

            if ($license->ended_at != $old_ended)
                array_push($modificaciones, ['fecha_fin' => $license->ended_at]);

            $modulos_old = $license->modules()->where('main', 'SI')->get();

            $modulos_delete = [];

            if ($old_company != $license->company_id || $old_ended != $license->ended_at)
                $license->notified = 'NO';
            
            if (!$license->update())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_main, $modules));

            foreach ($modulos_old as $key => $value) 
            {
                if (!in_array($value->id, $modules_main))
                {
                    array_push($modulos_delete, $value->display_name);
                }
            }

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);
            
            DB::commit();
            
            $mails = $request->has('add_email') ? $this->getDataFromMultiselect($request->get('add_email')) : [];

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Modificación', $modificaciones, $modulos_delete);

        } catch(\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la licencia'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  License  $license
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $license = License::system()->findOrFail($id);
        
        if(!$license->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la licencia'
        ]);
    }

    public function export(Request $request)
    {
        try
        {
          $modules = $this->getValuesForMultiselect($request->modules);
          $filtersType = $request->filtersType;
  
          $dates = [];
          $dates_request = explode('/', $request->dateRange);
  
          if (COUNT($dates_request) == 2)
          {
              array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
              array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
          }
  
          $filters = [
              'modules' => $modules,
              'dates' => $dates,
              'filtersType' => $filtersType
          ];
  
          ExportLicensesJob::dispatch($this->user, $this->company, $filters);
        
          return $this->respondHttp200();
  
        } catch(Exception $e) {
          return $this->respondHttp500();
        }
    }
}
