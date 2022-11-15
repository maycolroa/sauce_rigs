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
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Auth;
use DB;

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

        $regionalsFilter = [];
        $headquartersFilter = [];
        $processesFilter = [];
        $areasFilter = [];
        $locationLevelForm = '';
        $configLevel = '';

        
        try
        {
            $configLevel = ConfigurationsCompany::company($request->company_id)->findByKey('filter_inspections');
            
        } catch (\Exception $e) {
            $configLevel = 'NO';
        }

        if ($configLevel == 'SI')
        {
            $locationLevelForm = ConfigurationsCompany::company($request->company_id)->findByKey('location_level_form_user_inspection_filter');

            if ($locationLevelForm)
            {
                $id = $this->user->id;

                if ($id)
                {
                    if ($locationLevelForm == 'Regional')
                    {                        
                        $regionalsFilter = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();

                    }
                    else if ($locationLevelForm == 'Sede')
                    {
                        $regionalsFilter = DB::table('sau_ph_user_regionals')->where('user_id', $id)->pluck('employee_regional_id')->unique();
                        $headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                    }
                    else if ($locationLevelForm == 'Proceso')
                    {
                        $regionalsFilter = User::find($id)->regionals()->pluck('id');
                        $headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                        $processesFilter = User::find($id)->processes()->pluck('id');
                    }
                    else if ($locationLevelForm == 'Área')
                    {
                        $regionalsFilter = User::find($id)->regionals()->pluck('id');
                        $headquartersFilter = User::find($id)->headquartersFilter()->pluck('id');
                        $processesFilter = User::find($id)->processes()->pluck('id');
                        $areasFilter = User::find($id)->areas()->pluck('id');
                    }
                }
            }
        }

        $result = collect([]);
        $result->put('level_info', [
            $keywords['regional'],
            $keywords['headquarter'],
            $keywords['process'],
            $keywords['area']
        ]);

        $result->put('level', $level);

        if ($configLevel == 'SI' && $locationLevelForm != '' && COUNT($regionalsFilter) > 0)
        {
            \Log::info('SI');
            if ($locationLevelForm == 'Regional' && COUNT($regionalsFilter) > 0)
            {
                \Log::info('R');
                $regionals = $regionals = EmployeeRegional::select('id', 'name');
                $regionals = $regionals->whereIn('id', $regionalsFilter);
                $regionals->company_scope = $request->company_id;
                $regionals = $regionals->orderBy('name')->get();

                $regionals->transform(function($regional, $keyR) use ($level) {
                    return $regional;
                });

                $result->put('regionals', $regionals);
            }
            else if ($locationLevelForm == 'Sede' && COUNT($headquartersFilter) > 0)
            {
                \Log::info('H');
                \Log::info($regionalsFilter);
                \Log::info($headquartersFilter);
                $regionals = $regionals = EmployeeRegional::select('id', 'name');
                $regionals->whereIn('id', $regionalsFilter);
                $regionals->company_scope = $request->company_id;
                $regionals = $regionals->orderBy('name')->get();

                $regionals->transform(function($regional, $keyR) use ($level, $headquartersFilter) {

                    if ($level >= 2)
                    {
                        $headquarters = EmployeeHeadquarter::select('id', 'name')
                        ->where('employee_regional_id', $regional->id)
                        ->whereIn('id', $headquartersFilter)
                        ->orderBy('name')
                        ->get();

                        $regional->headquarters = $headquarters;
                    }

                    return $regional;
                });

                $result->put('regionals', $regionals);
            }
            else if ($locationLevelForm == 'Proceso' && COUNT($processesFilter) > 0)
            {
                \Log::info('P');
                $regionals = $regionals = EmployeeRegional::select('id', 'name');
                $regionals->whereIn('id', $regionalsFilter);
                $regionals->company_scope = $request->company_id;
                $regionals = $regionals->orderBy('name')->get();

                $regionals->transform(function($regional, $keyR) use ($level, $headquartersFilter, $processesFilter) {

                    if ($level >= 2)
                    {
                        $headquarters = EmployeeHeadquarter::select('id', 'name')
                        ->where('employee_regional_id', $regional->id)
                        ->whereIn('id', $headquartersFilter)
                        ->orderBy('name')
                        ->get();

                        if ($level >= 3)
                        {
                            $headquarters->transform(function($headquarter, $keyH) use ($level, $processesFilter) {

                                $processes = EmployeeProcess::selectRaw(
                                    "sau_employees_processes.id as id,
                                    sau_employees_processes.name as name")
                                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                                ->where('sau_headquarter_process.employee_headquarter_id', $headquarter->id)
                                ->whereIn('id', $processesFilter)
                                ->orderBy('sau_employees_processes.name')
                                ->get();

                                $headquarter->processes = $processes;

                                return $headquarter;
                            });
                        }

                        $regional->headquarters = $headquarters;
                    }

                    return $regional;
                });

                $result->put('regionals', $regionals);
            }
            else if ($locationLevelForm == 'Área' && COUNT($areasFilter) > 0)
            {
                \Log::info('A');
                $regionals = $regionals = EmployeeRegional::select('id', 'name');
                $regionals->whereIn('id', $regionalsFilter);
                $regionals->company_scope = $request->company_id;
                $regionals = $regionals->orderBy('name')->get();

                $regionals->transform(function($regional, $keyR) use ($level, $headquartersFilter, $processesFilter, $areasFilter) {

                    if ($level >= 2)
                    {
                        $headquarters = EmployeeHeadquarter::select('id', 'name')
                        ->where('employee_regional_id', $regional->id)
                        ->whereIn('id', $headquartersFilter)
                        ->orderBy('name')
                        ->get();

                        if ($level >= 3)
                        {
                            $headquarters->transform(function($headquarter, $keyH) use ($level, $processesFilter, $areasFilter) {

                                $processes = EmployeeProcess::selectRaw(
                                    "sau_employees_processes.id as id,
                                    sau_employees_processes.name as name")
                                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                                ->where('sau_headquarter_process.employee_headquarter_id', $headquarter->id)
                                ->whereIn('id', $processesFilter)
                                ->orderBy('sau_employees_processes.name')
                                ->get();

                                if ($level >= 4)
                                {
                                    $processes->transform(function($process, $keyP) use ($level, $headquarter, $areasFilter) 
                                    {
                                        $areas = EmployeeArea::selectRaw(
                                            "sau_employees_areas.id as id,
                                            sau_employees_areas.name as name")
                                        ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
                                        ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
                                        ->where('sau_process_area.employee_headquarter_id', $headquarter->id)
                                        ->where('sau_process_area.employee_process_id', $process->id)
                                        ->whereIn('id', $areasFilter)
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
            }
        }
        else
        {
            \Log::info('NO');
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
        }
        
        return $this->respondHttp200($result->toArray());
    }
}