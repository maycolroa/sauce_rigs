<?php

namespace App\Http\Controllers\System\Licenses;

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

        try
        {
            foreach ($request as $key => $value)
            {
                if ($value && $key != 'multiselect_user_id')
                {
                    if ($value && $key != 'multiselect_user_incapacitated_id')
                    {               
                        if ($key == 'users_notify_element_expired' || $key == 'users_notify_expired_absenteeism_expired' || $key == 'users_notify_expired_report' || $key == 'users_notify_incapacitated')
                            continue;
                            
                        if ($key == 'users_notify_report_license')
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

                        if ($key == 'license_reports_sends')
                        {
                            $values = $this->getDataFromMultiselect($value);

                            $reports = [];

                            foreach ($values as $id) 
                            {
                                array_push($reports, $id);
                            }

                            $value = implode(',', $reports);
                        }

                        ConfigurationsCompany::key($key)->value($value)->save();
                    }
                }
            }

            $this->saveLogActivitySystem('Licencias - Configuración Envio Reporte', 'Se creo o modifico la configuracion del envio');

            return $this->respondHttp200([
                'message' => 'Se actualizó la configuración'
            ]);
        }  catch(\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
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
                if ($key == 'users_notify_report_license')
                {
                    if ($value)
                    {
                        $users = explode(',', $value);

                        $multiselect = [];

                        foreach ($users as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            array_push($multiselect, $user->multiselect());
                        }
                    }
                }   

                if ($key == 'license_reports_sends')
                {
                    if ($value)
                    {
                        $reports = explode(',', $value);

                        $multiselect_reports = [];

                        foreach ($reports as $report) 
                        {
                            if ($report == 'general')
                            {
                                $content = [
                                    'name' => 'Reporte General',
                                    'value' => $report
                                ];
                            }
                            else if ($report == 'module')
                            {
                                $content = [
                                    'name' => 'Reporte por Módulo',
                                    'value' => $report
                                ];
                            }
                            else if ($report == 'group')
                            {
                                $content = [
                                    'name' => 'Reporte por Grupo de Compañia',
                                    'value' => $report
                                ];
                            }
                            else if ($report == 'group_module')
                            {
                                $content = [
                                    'name' => 'Reporte por Grupo de Compañia - Módulo',
                                    'value' => $report
                                ];
                            }

                            array_push($multiselect_reports, $content);
                        }
                    }
                } 
            }

            if (isset($multiselect) && count($multiselect) > 0)
            {
                $data['users_notify_report_license'] = $multiselect;
                $data['multiselect_user_id'] = $multiselect;
            }

            if (isset($multiselect_reports) && count($multiselect_reports) > 0)
            {
                $data['license_reports_sends'] = $multiselect_reports;
            }

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
