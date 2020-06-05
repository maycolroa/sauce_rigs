<?php

namespace App\Http\Controllers\Api;

use Hash;
use App\Http\Requests;
use App\Http\Requests\Api\CompanyRequiredRequest;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Facades\General\PermissionService;
use App\Traits\LocationFormTrait;
use App\Traits\UtilsTrait;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use Auth;

class LocationController extends ApiController
{
    use UtilsTrait;
    
    public function __construct()
    {
        $this->middleware('auth:api');
        parent::__construct();
    }
    
    public function levelLocation(CompanyRequiredRequest $request)
    {    
        $location = $this->getLocationFormConfModule($request->company_id);

        $keywords = $this->getKeywordQueue($request->company_id);

        $level = "";

        if ($location['area'] == 'SI')
            $level = 4;
        else if ($location['process'] == 'SI')
            $level = 3;
        else if ($location['headquarter'] == 'SI')
            $level = 2;
        else
            $level = 1;

        $result = collect([]);
        $result->put('level_info', [
            '1. '.$keywords['regional'],
            '2. '.$keywords['headquarter'],
            '3. '.$keywords['process'],
            '4. '.$keywords['area']
        ]);

        $result->put('level', $level);

        $regionals = $regionals = EmployeeRegional::select('id', 'name');
        $regionals->company_scope = $request->company_id;
        $regionals = $regionals->orderBy('name')->get();

        $regionals->transform(function($regional, $keyR) use ($level) {

            if ($level >= 2)
            {
                $headquarters = EmployeeHeadquarter::select('id', 'name')
                ->where('employee_regional_id', $regional->id)
                ->orderBy('name')
                ->get();

                if ($level >= 3)
                {
                    $headquarters->transform(function($headquarter, $keyH) use ($level) {

                        $processes = EmployeeProcess::selectRaw(
                            "sau_employees_processes.id as id,
                            sau_employees_processes.name as name")
                        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                        ->where('sau_headquarter_process.employee_headquarter_id', $headquarter->id)
                        ->orderBy('sau_employees_processes.name')
                        ->get();

                        if ($level >= 4)
                        {
                            $processes->transform(function($process, $keyP) use ($level, $headquarter) 
                            {
                                $areas = EmployeeArea::selectRaw(
                                    "sau_employees_areas.id as id,
                                    sau_employees_areas.name as name")
                                ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
                                ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
                                ->where('sau_process_area.employee_headquarter_id', $headquarter->id)
                                ->where('sau_process_area.employee_process_id', $process->id)
                                ->orderBy('sau_employees_areas.name')
                                ->get();

                                $process->areas = $areas;

                                return $process;
                            });
                        }

                        $headquarter->processes = $processes;

                        return $headquarter;
                    });
                }

                $regional->headquarters = $headquarters;
            }

            return $regional;
        });

        $result->put('regionals', $regionals);
        
        return $this->respondHttp200($result->toArray());
    }
}