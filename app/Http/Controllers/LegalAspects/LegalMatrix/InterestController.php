<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Interest;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Http\Requests\LegalAspects\LegalMatrix\InterestRequest;
use App\Jobs\LegalAspects\LegalMatrix\ConfigureInterestsJob;
use App\Jobs\LegalAspects\LegalMatrix\SyncQualificationsCompaniesJob;

class InterestController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:interests_c|interestsCustom_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:interests_r|interestsCustom_r, {$this->team}", ['except' => ['multiselect', 'multiselectSystem', 'multiselectCompany', 'saveInterests', 'myInterests', 'radioSystem']]);
        $this->middleware("permission:interests_u|interestsCustom_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:interests_d|interestsCustom_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:interests_config, {$this->team}", ['only' => ['saveInterests', 'myInterests', 'radioSystem']]);
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
            $interests = Interest::company()->select('*');
        else
            $interests = Interest::system()->select('*');

        if (!$request->has('orderBy'))
            $interests->orderBy('name');

        return Vuetable::of($interests)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InterestRequest $request)
    {
        $interest = new Interest($request->all());

        if ($request->custom == 'true')
            $interest->company_id = $this->company;
        
        if (!$interest->save())
            return $this->respondHttp500();

        if ($request->custom == 'true')
        {
            $company = Company::find($this->company);
            $company->interests()->attach($interest->id);
        }

        return $this->respondHttp200([
            'message' => 'Se creo el interes'
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
            $interest = Interest::findOrFail($id);
            $interest->custom = $interest->company_id ? true : false;  

            return $this->respondHttp200([
                'data' => $interest,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(InterestRequest $request, Interest $interest)
    {
        $interest->fill($request->all());
        
        if(!$interest->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el interes'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interest $interest)
    {
        if (COUNT($interest->articles) > 0)
        {
            return $this->respondWithError('No se puede eliminar el interés porque hay registros asociados a el');
        }

        if (!$interest->delete())
        {
            return $this->respondHttp500();
        }

        //SyncQualificationsCompaniesJob::dispatch();
        
        return $this->respondHttp200([
            'message' => 'Se elimino el intereses'
        ]);
    }

    public function saveInterests(Request $request)
    {
        try
        {
            if ($request->has('values'))
                $values = $request->get('values');
            else 
                $values = [];

            ConfigureInterestsJob::dispatch($this->company, $values);
        
            return $this->respondHttp200([
                'message' => 'Al culminar el proceso de configuración de sus intereses recibirá una notificación en su correo electrónico'
            ]);

        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function myInterests()
    {
        try
        {
            $company = Company::find($this->company);
            $values = $company->interests()->system()->pluck('sau_lm_interests.id');

            return $this->respondHttp200([
                'data' => [
                    "values"=> $values
                ]
            ]);
        } 
        catch(Exception $e)
        {
            $this->respondHttp500();
        }
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

    public function multiselect(Request $request, $scope = 'alls')
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $interests = Interest::select("id", "name")
                ->$scope()
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($interests)
            ]);
        }
        else
        {
            $interests = Interest::select(
                'sau_lm_interests.id as id',
                'sau_lm_interests.name as name'
            )
            ->$scope()
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($interests);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function radioSystem(Request $request)
    {
        $interests = Interest::select(
            'sau_lm_interests.id as id',
            'sau_lm_interests.name as name'
        )
        ->system()
        ->orderBy('name')
        ->pluck('id', 'name');
    
        return $this->radioFormat($interests);
    }
}
