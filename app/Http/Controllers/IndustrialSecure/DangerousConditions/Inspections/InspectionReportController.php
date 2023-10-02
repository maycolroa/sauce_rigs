<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionReportExportJob;
use App\Inform\IndustrialSecure\DangerousConditions\Inspections\InformManagerInspections;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use App\Traits\LocationFormTrait;
use Carbon\Carbon;
use Validator;
use DB;

class InspectionReportController extends Controller
{
    use Filtertrait, LocationFormTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        //$this->middleware('permission:ph_inspections_c', ['only' => 'store']);
        $this->middleware("permission:ph_inspections_r, {$this->team}");
        //$this->middleware('permission:ph_inspections_u', ['only' => 'update']);
        //$this->middleware('permission:ph_inspections_d', ['only' => 'destroy']);
        $this->middleware("permission:ph_inspections_report_export, {$this->team}", ['only' => 'export']);
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
      $module_id = Module::where('name', 'dangerousConditions')->first()->id;

      if ($request->table == "with_theme" )
        $column = 'sau_ph_inspection_sections.name as section';
      else
        $column = 'sau_employees_regionals.name as regional';

      $consultas = InspectionItemsQualificationAreaLocation::select(
        'sau_ph_inspections.name AS name',
        'sau_employees_regionals.name AS regional',
        'sau_employees_headquarters.name AS headquarter',
        'sau_employees_processes.name AS process',
        'sau_employees_areas.name AS area',
        "{$column}",
        DB::raw('COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS numero_inspecciones'),
        DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento'),
        DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_no_cumplimiento'),
        DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento_parcial'),
        DB::raw('
            CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
            THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 2)
            ELSE 0 END AS porcentaje_items_cumplimiento'),
        DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
            THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
            ELSE 0 END AS porcentaje_items_no_cumplimiento'),
        DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
        THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
        ELSE 0 END AS porcentaje_items_cumplimiento_parcial'),
        DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados"),
        DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados")
      )
      ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
      ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
      ->join('sau_ph_inspections', function ($join) 
      {
        $join->on("sau_ph_inspections.company_id", DB::raw($this->company));
        $join->on("sau_ph_inspections.type_id", DB::raw(1));
        $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
      })
      ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
      ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
      ->leftJoin('sau_employees_headquarters','sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
      ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
      ->leftJoin('sau_employees_areas','sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
      ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
      {
        $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
        $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
        $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
      })
      ->leftJoin('sau_action_plans_activities', function ($join) 
      {
        $join->on("sau_action_plans_activities.company_id", DB::raw($this->company));
        $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
      })
      ->where('sau_ph_inspections.company_id', $this->company);

        if ($request->table == "with_theme" )
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'section');
        else
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional');

        $url = "/industrialsecure/dangerousconditions/inspection/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["qualifiers"]))
              $consultas->inQualifiers($this->getValuesForMultiselect($filters["qualifiers"]), $filters['filtersType']['qualifiers']);

            if (isset($filters["regionals"]))
              $consultas->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
              $consultas->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
              $consultas->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
              $consultas->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            if (isset($filters["inspections"]))
              $consultas->inInspections($this->getValuesForMultiselect($filters["inspections"]), $filters['filtersType']['inspections'], 'sau_ph_inspections');

            if (isset($filters["themes"]))
              $consultas->inThemes($this->getValuesForMultiselect($filters["themes"]), $filters['filtersType']['themes'], 'sau_ph_inspection_sections');

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $consultas->betweenDate($dates);
        }

        return Vuetable::of($consultas)
          ->make();  
    }

    public function dataType2(Request $request)
    {
      $module_id = Module::where('name', 'dangerousConditions')->first()->id;

      if ($request->table == "with_theme" )
        $column = 'sau_ph_inspection_sections.name as section';
      else
        $column = 'sau_employees_regionals.name as regional';

        $consultas = InspectionItemsQualificationAreaLocation::select(
          'sau_ph_inspections.name AS name',
          'sau_employees_regionals.name AS regional',
          'sau_employees_headquarters.name AS headquarter',
          'sau_employees_processes.name AS process',
          'sau_employees_areas.name AS area',
          "{$column}",
          DB::raw('COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS numero_inspecciones'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_no_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento_parcial'),
          DB::raw('
              CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
              THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 2)
              ELSE 0 END AS porcentaje_items_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
              THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
              ELSE 0 END AS porcentaje_items_no_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
          THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
          ELSE 0 END AS porcentaje_items_cumplimiento_parcial'),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados"),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados")
        )
        ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->join('sau_ph_inspections', function ($join) 
        {
          $join->on("sau_ph_inspections.company_id", DB::raw($this->company));
          $join->on("sau_ph_inspections.type_id", DB::raw(2));
          $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
        })
        ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
        ->leftJoin('sau_employees_headquarters','sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
        ->leftJoin('sau_employees_areas','sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
        {
          $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
          $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
          $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
        })
        ->leftJoin('sau_action_plans_activities', function ($join) 
        {
          $join->on("sau_action_plans_activities.company_id", DB::raw($this->company));
          $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
        })
        ->where('sau_ph_inspections.company_id', $this->company);

        if ($request->table == "with_theme" )
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'section');
        else
          $consultas->groupBy('name', 'area', 'headquarter', 'process', 'regional');

        $url = "/industrialsecure/dangerousconditions/inspection/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["qualifiers"]))
              $consultas->inQualifiers($this->getValuesForMultiselect($filters["qualifiers"]), $filters['filtersType']['qualifiers']);

            if (isset($filters["regionals"]))
              $consultas->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
              $consultas->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
              $consultas->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
              $consultas->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            if (isset($filters["inspections"]))
              $consultas->inInspections($this->getValuesForMultiselect($filters["inspections"]), $filters['filtersType']['inspections'], 'sau_ph_inspections');

            if (isset($filters["themes"]))
              $consultas->inThemes($this->getValuesForMultiselect($filters["themes"]), $filters['filtersType']['themes'], 'sau_ph_inspection_sections');

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $consultas->betweenDate($dates);
        }

        return Vuetable::of($consultas)
          ->make();  
             
    }

    public function getTotals(Request $request)
    {
        $url = "/industrialsecure/dangerousconditions/inspection/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $qualifiers = !$init ? $this->getValuesForMultiselect($request->qualifiers) : (isset($filters['qualifiers']) ? $this->getValuesForMultiselect($filters['qualifiers']) : []);

        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);

        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
        $inspections = !$init ? $this->getValuesForMultiselect($request->inspections) : (isset($filters['inspections']) ? $this->getValuesForMultiselect($filters['inspections']) : []);

        $themes = !$init ? $this->getValuesForMultiselect($request->themes) : (isset($filters['themes']) ? $this->getValuesForMultiselect($filters['themes']) : []);

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        $dates = [];

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }
        }

        $module_id = Module::where('name', 'dangerousConditions')->first()->id;

        $consultas = InspectionItemsQualificationAreaLocation::select(
          DB::raw('COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS numero_inspecciones'),
          DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_no_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento_parcial'),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados"),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados")
        )
        ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->join('sau_ph_inspections', function ($join) 
        {
          $join->on("sau_ph_inspections.company_id", DB::raw($this->company));
          $join->on("sau_ph_inspections.type_id", DB::raw(1));
          $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
        })
        ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
        {
          $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
          $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
          $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
        })
        ->leftJoin('sau_action_plans_activities', function ($join) 
        {
          $join->on("sau_action_plans_activities.company_id", DB::raw($this->company));
          $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
        })
        ->betweenDate($dates)
        ->where('sau_ph_inspections.company_id', $this->company)
        ->groupBy('employee_regional_id', 'employee_headquarter_id', 'employee_process_id', 'employee_area_id');

        if (COUNT($themes) > 0)
          $consultas->inThemes($themes, $filtersType['themes'], 'sau_ph_inspection_sections');

        if (COUNT($inspections) > 0)
          $consultas->inInspections($inspections, $filtersType['inspections'], 'sau_ph_inspections');

        if (COUNT($regionals) > 0)
          $consultas->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($qualifiers) > 0)
          $consultas->inQualifiers($qualifiers, $filtersType['qualifiers']);

        if (COUNT($headquarters) > 0)
          $consultas->inHeadquarters($headquarters, $filtersType['headquarters']);
        
        if (COUNT($processes) > 0)
          $consultas->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
          $consultas->inAreas($areas, $filtersType['areas']);

        $consultas = $consultas->get();

        $result = collect([]);
        $result->put('inspections', 0);
        $result->put('numero_items', 0);
        $result->put('t_cumple', 0);
        $result->put('t_no_cumple', 0);
        $result->put('t_cumple_p', 0);
        $result->put('pa_no_realizados', 0);
        $result->put('pa_realizados', 0);
        $result->put('p_cumple', 0);
        $result->put('p_no_cumple', 0);
        $result->put('p_parcial', 0);

        foreach ($consultas as $key => $value)
        {
          $result->put('inspections', $result->get('inspections') + $value->numero_inspecciones);
          $result->put('numero_items', $result->get('numero_items') + $value->numero_items);
          $result->put('t_cumple', $result->get('t_cumple') + $value->numero_items_cumplimiento);
          $result->put('t_no_cumple', $result->get('t_no_cumple') + $value->numero_items_no_cumplimiento);
          $result->put('t_cumple_p', $result->get('t_cumple_p') + $value->numero_items_cumplimiento_parcial);
          $result->put('pa_no_realizados', $result->get('pa_no_realizados') + $value->numero_planes_no_ejecutados);
          $result->put('pa_realizados', $result->get('pa_realizados') + $value->numero_planes_ejecutados);
        }

        if ($result->get('numero_items') > 0)
          $result->put('p_cumple', round(($result->get('t_cumple') / $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_cumple', '0%');

        if ($result->get('numero_items') > 0)
          $result->put('p_no_cumple', round(($result->get('t_no_cumple')/ $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_no_cumple', '0%');

        if ($result->get('numero_items') > 0)
          $result->put('p_parcial', round(($result->get('t_cumple_p') / $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_parcial', '0%');
    
        return $this->respondHttp200([
            'data' => $result
        ]);
    }

    public function getTotalsType2(Request $request)
    {
        $url = "/industrialsecure/dangerousconditions/inspection/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

        $qualifiers = !$init ? $this->getValuesForMultiselect($request->qualifiers) : (isset($filters['qualifiers']) ? $this->getValuesForMultiselect($filters['qualifiers']) : []);

        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);

        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
        $inspections = !$init ? $this->getValuesForMultiselect($request->inspections) : (isset($filters['inspections']) ? $this->getValuesForMultiselect($filters['inspections']) : []);

        $themes = !$init ? $this->getValuesForMultiselect($request->themes) : (isset($filters['themes']) ? $this->getValuesForMultiselect($filters['themes']) : []);

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        $dates = [];

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }
        }

        $module_id = Module::where('name', 'dangerousConditions')->first()->id;

        $consultas = InspectionItemsQualificationAreaLocation::select(
          DB::raw('COUNT(DISTINCT sau_ph_inspection_items_qualification_area_location.qualification_date) AS numero_inspecciones'),
          DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_no_cumplimiento'),
          DB::raw('COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) AS numero_items_cumplimiento_parcial'),
          DB::raw('
          CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
          THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 2)
          ELSE 0 END AS porcentaje_items_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
          THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
          ELSE 0 END AS porcentaje_items_no_cumplimiento'),
          DB::raw('CASE WHEN COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) > 0 
          THEN ROUND( ( COUNT(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_qualifications_inspections.id, NULL)) / COUNT(sau_ph_inspection_items_qualification_area_location.qualification_id) ) * 100, 1)
          ELSE 0 END AS porcentaje_items_cumplimiento_parcial'),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados"),
          DB::raw("COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados")
        )
        ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
        ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
        ->join('sau_ph_inspections', function ($join) 
        {
          $join->on("sau_ph_inspections.company_id", DB::raw($this->company));
          $join->on("sau_ph_inspections.type_id", DB::raw(2));
          $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
        })
        ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
        {
          $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
          $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
          $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
        })
        ->leftJoin('sau_action_plans_activities', function ($join) 
        {
          $join->on("sau_action_plans_activities.company_id", DB::raw($this->company));
          $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
        })
        ->betweenDate($dates)
        ->where('sau_ph_inspections.company_id', $this->company)
        ->groupBy('employee_regional_id', 'employee_headquarter_id', 'employee_process_id', 'employee_area_id');

        if (COUNT($themes) > 0)
          $consultas->inThemes($themes, $filtersType['themes'], 'sau_ph_inspection_sections');

        if (COUNT($inspections) > 0)
          $consultas->inInspections($inspections, $filtersType['inspections'], 'sau_ph_inspections');

        if (COUNT($regionals) > 0)
          $consultas->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($qualifiers) > 0)
          $consultas->inQualifiers($qualifiers, $filtersType['qualifiers']);

        if (COUNT($headquarters) > 0)
          $consultas->inHeadquarters($headquarters, $filtersType['headquarters']);
        
        if (COUNT($processes) > 0)
          $consultas->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
          $consultas->inAreas($areas, $filtersType['areas']);

        $consultas = $consultas->get();

        $result = collect([]);
        $result->put('inspections', 0);
        $result->put('numero_items', 0);
        $result->put('t_cumple', 0);
        $result->put('t_no_cumple', 0);
        $result->put('t_cumple_p', 0);
        $result->put('pa_no_realizados', 0);
        $result->put('p_cumple', 0);
        $result->put('p_no_cumple', 0);
        $result->put('p_parcial', 0);

        foreach ($consultas as $key => $value)
        {
          $result->put('inspections', $result->get('inspections') + $value->numero_inspecciones);
          $result->put('numero_items', $result->get('numero_items') + $value->numero_items);
          $result->put('t_cumple', $result->get('t_cumple') + $value->numero_items_cumplimiento);
          $result->put('t_no_cumple', $result->get('t_no_cumple') + $value->numero_items_no_cumplimiento);
          $result->put('t_cumple_p', $result->get('t_cumple_p') + $value->numero_items_cumplimiento_parcial);
          $result->put('pa_no_realizados', $result->get('pa_no_realizados') + $value->numero_planes_no_ejecutados);
          $result->put('pa_realizados', $result->get('pa_realizados') + $value->numero_planes_ejecutados);
        }

        if ($result->get('numero_items') > 0)
        $result->put('p_cumple', round(($result->get('t_cumple') / $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_cumple', '0%');

        if ($result->get('numero_items') > 0)
          $result->put('p_no_cumple', round(($result->get('t_no_cumple')/ $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_no_cumple', '0%');

        if ($result->get('numero_items') > 0)
          $result->put('p_parcial', round(($result->get('t_cumple_p') / $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_parcial', '0%');
        
        return $this->respondHttp200([
            'data' => $result
        ]);
    }

    public function export(Request $request)
    {
      try
        {
          $regionals = $this->getValuesForMultiselect($request->regionals);
          $headquarters = $this->getValuesForMultiselect($request->headquarters);
          $processes = $this->getValuesForMultiselect($request->processes);
          $areas = $this->getValuesForMultiselect($request->areas);
          $themes = $this->getValuesForMultiselect($request->themes);
          $inspections = $this->getValuesForMultiselect($request->inspections);
          $qualifiers = $this->getValuesForMultiselect($request->qualifiers);
          $filtersType = $request->filtersType;
          $dates = [];
  
          if (isset($request->dateRange) && $request->dateRange)
          {
              $dates_request = explode('/', $request->dateRange);
  
              if (COUNT($dates_request) == 2)
              {
                  array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                  array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
              }
          }

            $filters = [
                'regionals' => $regionals,
                'headquarters' => $headquarters,
                'processes' => $processes,
                'areas' => $areas,
                'themes' => $themes,
                'inspections' => $inspections,
                'qualifiers' => $qualifiers,
                'dates' => $dates,
                'filtersType' => $filtersType,
                'table' => $request->table
            ];

            InspectionReportExportJob::dispatch($this->user, $this->company, $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function reportDinamic(Request $request)
    {
      $url = "/industrialsecure/dangerousconditions/inspection/report";
      $init = true;
      $filters = [];

      if ($request->has('filtersType'))
          $init = false;
      else 
          $filters = $this->filterDefaultValues($this->user->id, $url);

      $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

      $qualifiers = !$init ? $this->getValuesForMultiselect($request->qualifiers) : (isset($filters['qualifiers']) ? $this->getValuesForMultiselect($filters['qualifiers']) : []);

      $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);

      $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
      
      $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
      
      $themes = !$init ? $this->getValuesForMultiselect($request->themes) : (isset($filters['themes']) ? $this->getValuesForMultiselect($filters['themes']) : []);

      $inspections = !$init ? $this->getValuesForMultiselect($request->inspections) : (isset($filters['inspections']) ? $this->getValuesForMultiselect($filters['inspections']) : []);

      $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

      $dates = [];

      $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

      if (isset($datesF) && $datesF)
      {
          $dates_request = explode('/', $datesF);

          if (COUNT($dates_request) == 2)
          {
              array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
              array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
          }
      }

      $informManager = new InformManagerInspections($this->company, $regionals, $headquarters, $processes, $areas, $themes, $filtersType, $dates, $inspections, $qualifiers);

      return $this->respondHttp200($informManager->getInformData());
    }

    public function multiselectBar()
    {
      $keywords = $this->user->getKeywords();
      $confLocation = $this->getLocationFormConfModule();

      $select = [
          'Inspecciones' => "inspection", 
          'Temas' => "theme"
      ];

      if ($confLocation['regional'] == 'SI')
        $select[$keywords['regionals']] = 'regional';
      if ($confLocation['headquarter'] == 'SI')
        $select[$keywords['headquarters']] = 'headquarter';
      if ($confLocation['process'] == 'SI')
        $select[$keywords['processes']] = 'process';
      if ($confLocation['area'] == 'SI')
        $select[$keywords['areas']] = 'area';
  
      return $this->multiSelectFormat(collect($select));
    }

    public function dataGestion(Request $request)
    {
      $module_id = Module::where('name', 'dangerousConditions')->first()->id;

      $consultas = InspectionItemsQualificationAreaLocation::select(
        'sau_ph_inspections.name AS name',
        'sau_ph_inspection_sections.name AS section',
        'sau_ph_inspection_section_items.description AS item',
        'sau_employees_regionals.name AS regional',
        'sau_employees_headquarters.name AS headquarter',
        'sau_employees_processes.name AS process',
        'sau_employees_areas.name AS area',
        'sau_ph_inspection_items_qualification_area_location.qualification_date AS qualification_date',
        'sau_ph_qualifications_inspections.description AS qualification',
        DB::raw("IF(sau_action_plans_activities.id, 'SI', 'NO') AS tiene_plan_action"),
        'sau_users.name AS qualificator'
      )
      ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
      ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
      ->join('sau_ph_inspections', function ($join) 
      {
        $join->on("sau_ph_inspections.company_id", DB::raw($this->company));
        //$join->on("sau_ph_inspections.type_id", DB::raw(1));
        $join->on("sau_ph_inspection_sections.inspection_id", "sau_ph_inspections.id");
      })
      ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
      ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
      ->join('sau_users', 'sau_users.id', 'sau_ph_inspection_items_qualification_area_location.qualifier_id')
      ->leftJoin('sau_employees_headquarters','sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
      ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
      ->leftJoin('sau_employees_areas','sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
      ->leftJoin('sau_action_plans_activity_module', function ($join) use ($module_id)
      {
        $join->on("sau_action_plans_activity_module.module_id", DB::raw($module_id));
        $join->on("sau_action_plans_activity_module.item_table_name", DB::raw("'sau_ph_inspection_items_qualification_area_location'"));
        $join->on("sau_action_plans_activity_module.item_id", "sau_ph_inspection_items_qualification_area_location.id");
      })
      ->leftJoin('sau_action_plans_activities', function ($join) 
      {
        $join->on("sau_action_plans_activities.company_id", DB::raw($this->company));
        $join->on("sau_action_plans_activities.id", 'sau_action_plans_activity_module.activity_id');
      })
      ->where('sau_ph_inspections.company_id', $this->company)
      ->groupBy('name', 'area', 'headquarter', 'process', 'regional', 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'qualificator', 'section', 'item', 'sau_ph_qualifications_inspections.description','sau_action_plans_activities.id')
      ->orderBy('sau_ph_inspection_items_qualification_area_location.qualification_date', 'DESC');

        $url = "/industrialsecure/dangerousconditions/inspection/reportGestion";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["qualifiers"]))
              $consultas->inQualifiers($this->getValuesForMultiselect($filters["qualifiers"]), $filters['filtersType']['qualifiers']);

            if (isset($filters["regionals"]))
              $consultas->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
              $consultas->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
              $consultas->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
              $consultas->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            if (isset($filters["inspections"]))
              $consultas->inInspections($this->getValuesForMultiselect($filters["inspections"]), $filters['filtersType']['inspections'], 'sau_ph_inspections');

            /*if (isset($filters["themes"]))
              $consultas->inThemes($this->getValuesForMultiselect($filters["themes"]), $filters['filtersType']['themes'], 'sau_ph_inspection_sections');*/

            if (isset($filters["items"]))
              $consultas->inItems($this->getValuesForMultiselect($filters["items"]), $filters['filtersType']['items']);

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
              array_push($dates, $this->formatDateToSave($dates_request[0]));
              array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
            else
            {
              $now = Carbon::now();
              $last = Carbon::now()->subMonth(1);

              array_push($dates, $last->format('Y-m-d'));
              array_push($dates, $now->format('Y-m-d'));
            }
                
            $consultas->betweenDate($dates);
        }
        else
        {
          $now = Carbon::now();
          $last = Carbon::now()->subMonth(1);
          $dates = [];

          array_push($dates, $last->format('Y-m-d'));
          array_push($dates, $now->format('Y-m-d'));
              
          $consultas->betweenDate($dates);
        }

        return Vuetable::of($consultas)
          ->make();  
    }
}