<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Models\Administrative\Users\User;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;

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
        \Log::info($request);
        $request = $request->except('_method');

        foreach ($request as $key => $value)
        {
            if ($value && $key != 'multiselect_90_user_id' && $key != 'multiselect_180_user_id' && $key != 'multiselect_540_user_id' && $key != 'multiselect_user_id' && $key != 'multiselect_table')
            {
                if ($key == 'users_notify_element_expired' || $key == 'users_notify_stock_minimun' || $key == 'users_notify_expired_report' || $key == 'users_notify_incapacitated' || $key == 'users_notify_report_license' || $key == 'users_notify_criticality_level_inspections')
                    continue;
                    
                if ($key == 'users_notify_expired_absenteeism_expired_90' || $key == 'users_notify_expired_absenteeism_expired_180' || $key == 'users_notify_expired_absenteeism_expired_540')
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
                if ($key == 'name_table_absenteeism')
                {
                    if ($value)
                    {
                        $multiselect_table = [];

                        $table = Table::find($value);

                        if ($table)
                             array_push($multiselect_table, $table->multiselect());
                    }
                }   

                if ($key == 'users_notify_expired_absenteeism_expired_90')
                {
                    if ($value)
                    {
                        $users_90 = explode(',', $value);

                        $multiselect_90 = [];

                        foreach ($users_90 as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            if ($user)
                                array_push($multiselect_90, $user->multiselect());
                        }
                    }
                }   
                else if ($key == 'users_notify_expired_absenteeism_expired_180')
                {
                    if ($value)
                    {
                        $users_180 = explode(',', $value);

                        $multiselect_180 = [];

                        foreach ($users_180 as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            if ($user)
                                array_push($multiselect_180, $user->multiselect());
                        }
                    }
                }   
                else if ($key == 'users_notify_expired_absenteeism_expired_540')
                {
                    if ($value)
                    {
                        $users_540 = explode(',', $value);

                        $multiselect_540 = [];

                        foreach ($users_540 as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            if ($user)
                                array_push($multiselect_540, $user->multiselect());
                        }
                    }
                }   
            }

            if (isset($multiselect_90) && count($multiselect_90) > 0)
            {
                $data['users_notify_expired_absenteeism_expired_90'] = $multiselect_90;
                $data['multiselect_90_user_id'] = $multiselect_90;
            }

            if (isset($multiselect_180) && count($multiselect_180) > 0)
            {
                $data['users_notify_expired_absenteeism_expired_180'] = $multiselect_180;
                $data['multiselect_180_user_id'] = $multiselect_180;
            }

            if (isset($multiselect_540) && count($multiselect_540) > 0)
            {
                $data['users_notify_expired_absenteeism_expired_540'] = $multiselect_540;
                $data['multiselect_540_user_id'] = $multiselect_540;
            }

            if (isset($multiselect_table) && count($multiselect_table) > 0)
            {
                $data['name_table_absenteeism'] = (int) $data['name_table_absenteeism'];
                $data['multiselect_table'] = $multiselect_table;
            }

            \Log::info($data);

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
