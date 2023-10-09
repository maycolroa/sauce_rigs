<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

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
            if ($value && $key != 'multiselect_user_id')
            {
                if ($value && $key != 'multiselect_user_incapacitated_id')
                {
                  
                    if ($key == 'users_notify_element_expired' || $key == 'users_notify_expired_absenteeism_expired')
                        continue;
                        
                    if ($key == 'users_notify_expired_report' || $key == 'users_notify_incapacitated')
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

                    ConfigurationsCompany::key($key)->value($value)->save();
                }
            }
        }

        $this->saveLogActivitySystem('Reincorporaciones - Configuración', 'Se creo o modifico la configuracion de la compañia');

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
                if ($key == 'users_notify_expired_report')
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
                if ($key == 'users_notify_incapacitated')
                {
                    if ($value)
                    {
                        $users = explode(',', $value);

                        $multiselect_incapacitated = [];

                        foreach ($users as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            if ($user)
                                array_push($multiselect_incapacitated, $user->multiselect());
                        }
                    }
                } 
            }

            if (isset($multiselect) && count($multiselect) > 0)
            {
                $data['users_notify_expired_report'] = $multiselect;
                $data['multiselect_user_id'] = $multiselect;
            }

            if (isset($multiselect_incapacitated) && count($multiselect_incapacitated) > 0)
            {
                $data['users_notify_incapacitated'] = $multiselect_incapacitated;
                $data['multiselect_user_incapacitated_id'] = $multiselect_incapacitated;
            }

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
