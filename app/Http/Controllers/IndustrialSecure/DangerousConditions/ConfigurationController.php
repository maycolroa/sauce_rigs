<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Models\Administrative\Users\User;

class ConfigurationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:configurations_epp_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:configurations_epp_c, {$this->team}");*/
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configuration\ConfigurationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConfigurationRequest $request)
    {
        $request = $request->except('_method');

        foreach ($request as $key => $value)
        {
            if ($value)
            {
                if ($key == 'users_notify_criticality_level_inspections')
                {
                    $values = $this->getDataFromMultiselect($value);

                    $users = [];
                    
                    foreach ($values as $id) 
                    {
                        $user = User::find($id);

                        array_push($users, $user->email);
                    }

                    $value = implode(',', $users);
                }

                if ($key == 'multiselect_criticality_user_id')
                    continue;

                ConfigurationsCompany::key($key)->value($value)->save();
            }
        }

        $this->saveLogActivitySystem('Inspecciones - Configuracion', 'Se creo o edito la configuración');

        return $this->respondHttp200([
            'message' => 'Se actualizó la configuración'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try
        {
            $data = ConfigurationsCompany::findall();

            foreach ($data as $key => $value) 
            {
                if ($key == 'users_notify_criticality_level_inspections')
                {
                    if ($value)
                    {
                        $users = explode(',', $value);

                        $multiselect = [];

                        foreach ($users as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            if ($user)
                                array_push($multiselect, $user->multiselect());
                        }
                    }
                }

                if ($key == 'multiselect_criticality_user_id')
                    continue;
            }

            if (isset($multiselect) && count($multiselect) > 0)
            {
                $data['users_notify_criticality_level_inspections'] = $multiselect;
                $data['multiselect_criticality_user_id'] = $multiselect;
            }

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
