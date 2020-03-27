<?php

namespace App\Http\Controllers\System\Licenses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\License;
use App\Models\General\ModuleDependence;
use App\Http\Requests\System\Licenses\LicenseRequest;
use App\Jobs\System\Licenses\NotifyLicenseRenewalJob;
use Carbon\Carbon;
use DB;

class LicenseController extends Controller
{
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

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails);

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
            $license = License::system()->findOrFail($id);
            $old_company = $license->company_id;
            $old_ended = $license->ended_at;

            $license->fill($request->all());
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Y-m-d');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Y-m-d');

            if ($old_company != $license->company_id || $old_ended != $license->ended_at)
                $license->notified = 'NO';
            
            if (!$license->update())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_main, $modules));

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);
            
            DB::commit();
            
            $mails = $request->has('add_email') ? $this->getDataFromMultiselect($request->get('add_email')) : [];

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails);

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
}
