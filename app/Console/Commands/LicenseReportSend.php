<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\General\Module;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\System\Licenses\ReportExportExcel;
use App\Traits\UtilsTrait;

class LicenseReportSend extends Command
{
    use UtilsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license-report-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de reporte de licencia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getConfig()
    {
        $key = "filters_date_license";

        try
        {
            $exists = ConfigurationsCompany::company(1)->findByKey($key);

            if ($exists)
                return $exists;
            else
                return NULL;
        } catch (\Exception $e) {
            return NULL;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $headers = [];
            $dates = [];
            $dates_old = [];

            $filters = $this->getConfig();

            if (!$filters)
                exit;

            $dates_request = explode('/', $filters);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));

                array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->subYear(1)->format('Y-m-d'));
                array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->subYear(1)->format('Y-m-d'));
            }
            else
            {
                $start = Carbon::now()->startOfYear()->format('Y-m-d');
                $end = Carbon::now()->format('Y-m-d');
                $start_old = Carbon::now()->subYear(1)->startOfYear()->format('Y-m-d');
                $end_old = Carbon::now()->subYear(1)->format('Y-m-d');

                array_push($dates, $start);
                array_push($dates, $end);
                array_push($dates_old, $start_old);
                array_push($dates_old, $end_old);
            }

            $id_license_renew = [];
            $id_module_renew = [];
            $id_module_group_renew = [];
            $id_group_renew = [];
            $table_general = [];
            $table_module = [];
            $table_groups = [];
            $table_groups_modules = [];

            $prueba = License::selectRaw("
                sau_companies.id as company_id,
                sau_licenses.id as license_id,
                sau_modules.display_name as module,
                sau_licenses.started_at as fecha,
                sau_company_groups.name as group_name,
                sau_companies.name as name_company
            ")
            ->withoutGlobalScopes()
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            ->where('sau_companies.test', DB::raw("'NO'"))
            ->orderBy('sau_licenses.id')
            ->get();

            $companies = $prueba->groupBy('company_id')
            ->each(function($item, $key) use (&$id_license_renew) {
                $i = 0;

                foreach ($item->groupBy('license_id')->all() as $license_id => $license)
                {
                    if ($i > 0)
                        array_push($id_license_renew, $license_id);

                    $i++;
                }
            });

            $modules = $prueba->groupBy('company_id')
            ->each(function($modules, $companyId) use (&$id_module_renew) {
                $modules->groupBy('module')
                ->each(function($licenses, $moduleId) use (&$id_module_renew, $companyId) {
                    $i = 0;

                    foreach ($licenses as $license)
                    {
                        if (!isset($id_module_renew[$moduleId]))
                            $id_module_renew[$moduleId] = [];

                        if ($i > 0)
                            array_push($id_module_renew[$moduleId], $license->license_id);

                        $i++;
                    }
                });
            });

            $grupos_modulos = [];

            $modules = $prueba->groupBy('group_name')
            ->each(function($modules, $companyId) use (&$id_module_group_renew, &$grupos_modulos) {
                $modules->groupBy('module')
                ->each(function($licenses, $moduleId) use (&$id_module_group_renew, $companyId, &$grupos_modulos) {
                    $i = 0;

                    foreach ($licenses as $license)
                    {
                        if (!isset($id_module_group_renew[$moduleId]))
                        {
                            $id_module_group_renew[$moduleId] = [];
                        }

                        if (!isset($grupos_modulos[$license->group_name]))
                        {
                            if (!is_null($license->group_name))
                            {
                                $grupos_modulos[$license->group_name] = [];
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }
                        else
                        {
                            if (!is_null($license->group_name))
                            {
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }

                        if ($i > 0)
                        {
                            if (!isset($id_module_group_renew[$moduleId]))
                            {
                                $id_module_group_renew[$moduleId] = [];
                            }
                            else
                                array_push($id_module_group_renew[$moduleId], $license->license_id);
                            if (!is_null($license->group_name))
                            {
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }

                        $i++;
                    }
                });
            });

            $prueba = $prueba->map(function ($item, $key) use ($id_license_renew, $id_module_renew, $id_module_group_renew) {
                $item->renewed = in_array($item->license_id, $id_license_renew);
                $item->renewed_module = isset($id_module_renew[$item->module]) && in_array($item->license_id, $id_module_renew[$item->module]);
                $item->renewed_group_module = isset($id_module_group_renew[$item->module]) && in_array($item->license_id, $id_module_group_renew[$item->module]);

                return $item;
            });

            $headers['general'] = [                    
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
                'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
                'Total Periodo '.$dates[0].'/'.$dates[1],
                'Porcentaje de retención',
                'Porcentaje de crecimiento'
            ];

            $headers['group'] = [                       
                'Grupo de compañia',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
                'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
                'Total Periodo '.$dates[0].'/'.$dates[1],
                'Porcentaje de retención',
                'Porcentaje de crecimiento'
            ];

            $headers['module'] = [      
                'Módulo',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
                'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
                'Total Periodo '.$dates[0].'/'.$dates[1],
                'Porcentaje de retención',
                'Porcentaje de crecimiento'
            ];

            $headers['group_module'] = [      
                'Grupo de compañia',
                'Módulo',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
                'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
                'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
                'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
                'Total Periodo '.$dates[0].'/'.$dates[1],
                'Porcentaje de retención',
                'Porcentaje de crecimiento'
            ];


            if (COUNT($dates) > 0)
            {
                $range_actual = $prueba->filter(function ($item, $key) use ($dates) {
                    return Carbon::parse($item->fecha)->between($dates[0], $dates[1]);
                });

                $modules_actual = $range_actual->pluck('module');

                $range_old = $prueba->filter(function ($item, $key) use ($dates_old) {
                    return Carbon::parse($item->fecha)->between($dates_old[0], $dates_old[1]);
                });

                $modules_old = $range_old->pluck('module');

                $modules_all = $modules_actual->merge($modules_old)->unique()->values();

                $groups = $prueba->filter(function ($item, $key) {
                    return $item->group_name;
                })
                ->pluck('group_name')->unique()->values();

                foreach ($groups as $key => $group) {
                    $retention = $range_old->where('group_name', $group)->count() > 0 ? round(($range_actual->where('group_name', $group)->where('renewed', true)->where('renewed_module', true)->count()/$range_old->where('group_name', $group)->count())*100, 2) : 0;

                    $crecimiento = $range_old->where('group_name', $group)->count() > 0 ? round((($range_actual->where('group_name', $group)->count() - $range_old->where('group_name', $group)->count())/$range_old->where('group_name', $group)->count()) * 100, 2) : 0;

                    $content = [
                        'group' => $group,
                        'renew_old' => $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total_old' => $range_old->where('group_name', $group)->count(),
                        'renew' => $range_actual->where('group_name', $group)->where('renewed_module', true)->where('renewed', true)->count(),
                        'new' => $range_actual->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total' => $range_actual->where('group_name', $group)->count(),
                        'retention' => $retention,
                        'crecimiento' => $crecimiento
                    ];

                    array_push($table_groups, $content);
                }


                $retention_sg = $range_old->where('group_name', NULL)->count() > 0 ? round(($range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count()/$range_old->where('group_name', NULL)->count())*100, 2) : 0;

                $crecimiento_sg = $range_old->where('group_name', NULL)->count() > 0 ? round((($range_actual->where('group_name', NULL)->count() - $range_old->where('group_name', NULL)->count())/$range_old->where('group_name', NULL)->count()) * 100, 2) : 0;

                $content = [
                    'group' => 'Sin grupo',
                    'renew_old' => $range_old->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new_old' => $range_old->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total_old' => $range_old->where('group_name', NULL)->count(),
                    'renew' => $range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new' => $range_actual->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total' => $range_actual->where('group_name', NULL)->count(),
                    'retention' => $retention_sg,
                    'crecimiento' => $crecimiento_sg
                ];

                $retention_sg_t = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_sg_t = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed',false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed',false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_sg_t,
                    'crecimiento' => $crecimiento_sg_t
                ];

                array_push($table_groups, $content);

                $table_groups = collect($table_groups)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0 || $item['crecimiento'] > 0;
                })->values();

                $table_groups->push($content2);

                foreach ($modules_all as $key => $value) 
                {
                    $retention = $range_old->where('module', $value)->count() > 0 ? round(($range_actual->where('module', $value)->where('renewed_module', true)->count()/$range_old->where('module', $value)->count())*100, 2) : 0;

                    $crecimiento = $range_old->where('module', $value)->count() > 0 ? round((($range_actual->where('module', $value)->count() - $range_old->where('module', $value)->count())/$range_old->where('module', $value)->count()) * 100, 2) : 0;

                    $content = [
                        'module' => $value,
                        'renew_old' => $range_old->where('module', $value)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('module', $value)->where('renewed_module',false)->count(),
                        'total_old' => $range_old->where('module', $value)->count(),
                        'renew' => $range_actual->where('module', $value)->where('renewed_module', true)->count(),
                        'new' => $range_actual->where('module', $value)->where('renewed_module',false)->count(),
                        'total' => $range_actual->where('module', $value)->count(),
                        'retention' => $retention,
                        'crecimiento' => $crecimiento
                    ];

                    array_push($table_module, $content);
                }

                $table_module = collect($table_module)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0;
                })->values();

                $retention_m = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_m = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'module' => 'Total',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed', false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed', false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_m,
                    'crecimiento' => $crecimiento_m
                ];

                $table_module->push($content2);

                foreach ($groups as $key => $group) 
                {
                    foreach (collect($grupos_modulos[$group])->unique()->values() as $key => $value) 
                    {
                        $retention = $range_old->where('group_name', $group)->where('module', $value)->count() > 0 ? round(($range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count()/$range_old->where('group_name', $group)->where('module', $value)->count())*100, 2) : 0;

                        $crecimiento = $range_old->where('group_name', $group)->where('module', $value)->count() > 0 ? round((($range_actual->where('group_name', $group)->where('module', $value)->count() - $range_old->where('group_name', $group)->where('module', $value)->count())/$range_old->where('group_name', $group)->where('module', $value)->count()) * 100, 2) : 0;

                        $content = [
                            'group' => $group,
                            'module' => $value,
                            'renew_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count(),
                            'new_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',true)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', false)->count(),
                            'total_old' => $range_old->where('group_name', $group)->where('module', $value)->count(),
                            'renew' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count(),
                            'new' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',true)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed_module', false)->where('renewed',false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed_module', false)->where('renewed',true)->count(),
                            'total' => $range_actual->where('group_name', $group)->where('module', $value)->count(),
                            'retention' => $retention,
                            'crecimiento' => $crecimiento
                        ];

                        array_push($table_groups_modules, $content);
                    }

                }

                $table_groups_modules = collect($table_groups_modules)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0;
                })->values();

                $retention_g_t = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_g_t = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'module' => 'Todos',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed',false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed',false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_g_t,
                    'crecimiento' => $crecimiento_g_t
                ];


                $table_groups_modules->push($content2);

                $retention_general = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_general = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100) : 0;

                $table_general = [
                    [
                        'renew_old' => $range_old->where('renewed', true)->count(),
                        'new_old' => $range_old->where('renewed',false)->count(),
                        'total_old' => $range_old->count(),
                        'renew' => $range_actual->where('renewed', true)->count(),
                        'new' => $range_actual->where('renewed',false)->count(),
                        'total' => $range_actual->count(),
                        'retention' => $retention_general,
                        'crecimiento' => $crecimiento_general
                    ]
                ];
            }

///////////////Reporte grupo - compañia - modulos que no posee//////////////


            $table_not_module = [];

            $prueba2 = License::selectRaw("
                sau_companies.id as company_id,
                sau_licenses.id as license_id,
                sau_modules.display_name as module,
                sau_licenses.started_at as fecha,
                sau_company_groups.name as group_name,
                sau_companies.name as name_company
            ")
            ->withoutGlobalScopes()
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_companies.test', DB::raw("'NO'"));

            $modules_totales = Module::select('display_name')->where('main', DB::raw("'SI'"));

            if (isset($filters["modules"]) && $filters["modules"])
            {
                $modules_filters = $this->getValuesForMultiselect($filters["modules"]);
                $modules_totales->whereIn('sau_modules.id', $modules_filters);
            }
        
            $prueba2 = $prueba2->orderBy('sau_licenses.id')->get();

            $modules_totales = $modules_totales->pluck('display_name')->toArray();

            foreach ($groups as $key => $group) 
            {
                $companies = $prueba2->filter(function ($item, $key) use ($group) {
                    return $item->group_name == $group;
                })->pluck('name_company')->unique()->values();

                foreach ($companies as $key => $company) 
                {
                    $modules_disponibles = [];
                    $modules_company = $prueba2->filter(function ($item, $key) use ($group, $company){
                        return $item->group_name == $group && $item->name_company == $company;
                    })
                    ->pluck('module')->unique()->values();
                    
                    foreach ($modules_company as $key => $company_module) 
                    {
                        if (is_array($company_module) && COUNT($company_module) > 0)
                            array_push($modules_disponibles, $company_module);
                        else if (is_string($company_module))
                            array_push($modules_disponibles, $company_module);
                    }

                    $content = [];

                    foreach ($modules_totales as $key => $module) 
                    {
                    if (!in_array($module, $modules_disponibles))
                        array_push($content, $module);
                    }

                    if (COUNT($content) > 0)
                    {
                        $content = [
                            'group' => $group,
                            'company' => $company,
                            'module' => implode(' - ',$content)
                        ];

                        array_push($table_not_module, $content);
                    }
                }

                $companies_sin_grupo = $prueba2->filter(function ($item, $key) {
                    return !$item->group_name;
                })
                ->pluck('name_company')->unique()->values();

                foreach ($companies_sin_grupo as $key => $company) 
                {
                    $modules_disponibles = [];
                    $modules_company = $prueba2->filter(function ($item, $key) use ($company){
                        return !$item->group_name && $item->name_company == $company;
                    })
                    ->pluck('module')->unique()->values();
                    
                    foreach ($modules_company as $key => $company_module) 
                    {
                        if (is_array($company_module) && COUNT($company_module) > 0)
                            array_push($modules_disponibles, $company_module);
                        else if (is_string($company_module))
                            array_push($modules_disponibles, $company_module);
                    }

                    $content = [];

                    foreach ($modules_totales as $key => $module) 
                    {
                    if (!in_array($module, $modules_disponibles))
                        array_push($content, $module);
                    }

                    if (COUNT($content) > 0)
                    {
                        $content = [
                            'group' => 'Sin grupo',
                            'company' => $company,
                            'module' => implode(' - ',$content)
                        ];

                        array_push($table_not_module, $content);
                    }
                }
            }

            $headers['group_module_not'] = [      
                'Grupo de compañia',
                'compañia',
                'Módulo'
            ];


///////////////////////////////////////////////////////////////////////////

            $data = [
                'headers' => $headers,
                'data' => [
                    'general' => $table_general,
                    'module' => $table_module,
                    'group' => $table_groups,
                    'group_module' => $table_groups_modules,
                    'group_module_not' => $table_not_module
                ],

            ];

            $nameExcel = 'export/1/license_report_'.date("YmdHis").'.xlsx';
            Excel::store(new ReportExportExcel($data),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
            
            $paramUrl = base64_encode($nameExcel);

            $responsibles = ConfigurationsCompany::company(1)->findByKey('users_notify_report_license');

            if ($responsibles)
                    $responsibles = explode(',', $responsibles);

                if (count($responsibles) > 0)
                {
                    foreach ($responsibles as $email)
                    {
                        $recipient = new User(["email" => $email]); 
            
                        NotificationMail::
                            subject('Exportación de Reportes de Licencias')
                            ->recipients($recipient)
                            ->message('Se ha generado una exportación de reportes de licencia.')
                            ->subcopy('Este link es valido por 24 horas')
                            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                            ->module('users')
                            ->event('Tarea programada: LicenseReportSend')
                            ->company(1)
                            ->send();
                    }
                }

            //\Log::info($data);

            return $data;


        } catch(Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
