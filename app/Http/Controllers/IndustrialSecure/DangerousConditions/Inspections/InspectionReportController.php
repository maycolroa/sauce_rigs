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
use Carbon\Carbon;
use Validator;
use DB;

class InspectionReportController extends Controller
{
    use Filtertrait;

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
        $column = 's.name as section';
      else
        $column = 'l.name as headquarter';

      $consultas = InspectionItemsQualificationAreaLocation::select(
          'a.name as area',
          'l.name as headquarter',
          "{$column}",
          DB::raw('(
            select count(distinct i2.id) from sau_ph_inspection_items_qualification_area_location iq2
            inner join sau_ph_inspection_section_items it2 on iq2.item_id = it2.id
            inner join sau_ph_inspection_sections s2 on it2.inspection_section_id = s2.id
            inner join sau_ph_inspections i2 on s2.inspection_id = i2.id
            where iq2.employee_headquarter_id = sau_ph_inspection_items_qualification_area_location.employee_headquarter_id and iq2.employee_area_id = sau_ph_inspection_items_qualification_area_location.employee_area_id
            ) as numero_inspecciones'),
          DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
          DB::raw('count(IF(q.fulfillment = 1, q.id, null)) as numero_items_cumplimiento'),
          DB::raw('count(IF(q.fulfillment = 0, q.id, null)) as numero_items_no_cumplimiento'),
          DB::raw("sum(
            (SELECT IF(COUNT(IF(iap2.state=\"Pendiente\",0, NULL)) > 0, 1, 0) 
            FROM sau_action_plans_activities iap2 
            inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
            WHERE 
            iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
            iam2.module_id = {$module_id} and
            iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
            )AS numero_planes_no_ejecutados"),
          DB::raw("sum(
            (SELECT IF(COUNT(true), 1, 0) as actividades_totales
            FROM sau_action_plans_activities iap2 
            inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
            WHERE 
            iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
            iam2.module_id = {$module_id} and
            iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
            )AS actividades_totales")
        )
        ->join('sau_ph_inspection_section_items as it','sau_ph_inspection_items_qualification_area_location.item_id', 'it.id')
        ->join('sau_ph_inspection_sections as s','it.inspection_section_id', 's.id')
        ->join('sau_ph_inspections as i','s.inspection_id', 'i.id')
        ->join('sau_employees_areas as a','a.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
        ->join('sau_employees_headquarters as l','l.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
        ->join('sau_ct_qualifications as q','q.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
        ->where('i.company_id', $this->company);

        if ($request->table == "with_theme" )
          $consultas->groupBy('area', 'headquarter', 'numero_inspecciones', 'section');
        else
          $consultas->groupBy('area', 'headquarter', 'numero_inspecciones');

        $url = "/industrialsecure/dangerousconditions/inspection/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $consultas->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
            $consultas->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
            $consultas->inThemes($this->getValuesForMultiselect($filters["themes"]), $filters['filtersType']['themes'], 's');

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
          ->addColumn('porcentaje_items_cumplimiento', function ($consulta) {
            if ($consulta->numero_items > 0)
              return round(($consulta->numero_items_cumplimiento / $consulta->numero_items) * 100, 1)."%";
            else
              return '0%';
          })
          ->addColumn('porcentaje_items_no_cumplimiento', function ($consulta) {
            if ($consulta->numero_items > 0)
              return round(($consulta->numero_items_no_cumplimiento / $consulta->numero_items) * 100, 1)."%";
            else
              return '0%';
          })
          ->addColumn('numero_planes_ejecutados', function ($consulta) {
              return $consulta->actividades_totales - $consulta->numero_planes_no_ejecutados;
          })
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

        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
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
            DB::raw('(
             select count(distinct i2.id) from sau_ph_inspection_items_qualification_area_location iq2
             inner join sau_ph_inspection_section_items it2 on iq2.item_id = it2.id
             inner join sau_ph_inspection_sections s2 on it2.inspection_section_id = s2.id
             inner join sau_ph_inspections i2 on s2.inspection_id = i2.id
             where iq2.employee_headquarter_id = sau_ph_inspection_items_qualification_area_location.employee_headquarter_id and iq2.employee_area_id = sau_ph_inspection_items_qualification_area_location.employee_area_id
             ) as numero_inspecciones'),
             DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
             DB::raw('count(IF(q.fulfillment = 1, q.id, null)) as numero_items_cumplimiento'),
             DB::raw('count(IF(q.fulfillment = 0, q.id, null)) as numero_items_no_cumplimiento'),
             DB::raw("sum(
               (SELECT IF(COUNT(IF(iap2.state=\"Pendiente\",0, NULL)) > 0, 1, 0) 
               FROM sau_action_plans_activities iap2 
               inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
               WHERE 
                iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
                iam2.module_id = {$module_id} and
                iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
               )AS numero_planes_no_ejecutados"),
             DB::raw("sum(
               (SELECT IF(COUNT(true), 1, 0) as actividades_totales
               FROM sau_action_plans_activities iap2 
               inner join sau_action_plans_activity_module iam2 on iam2.activity_id = iap2.id
               WHERE 
                iam2.item_table_name = 'sau_ph_inspection_items_qualification_area_location' and 
                iam2.module_id = {$module_id} and
                iam2.item_id = sau_ph_inspection_items_qualification_area_location.id)
               )AS actividades_totales")
             )
             ->join('sau_ph_inspection_section_items as it','sau_ph_inspection_items_qualification_area_location.item_id', 'it.id')
             ->join('sau_ph_inspection_sections as s','it.inspection_section_id', 's.id')
             ->join('sau_ph_inspections as i','s.inspection_id', 'i.id')
             ->join('sau_ct_qualifications as q','q.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
             ->inHeadquarters($headquarters, $filtersType['headquarters'])
             ->inAreas($areas, $filtersType['areas'])
             ->inThemes($themes, $filtersType['themes'], 's')
             ->betweenDate($dates)
             ->where('i.company_id', $this->company)
             ->groupBy('employee_area_id', 'employee_headquarter_id', 'numero_inspecciones')
             ->get(); 

        $result = collect([]);
        $result->put('inspections', 0);
        $result->put('numero_items', 0);
        $result->put('t_cumple', 0);
        $result->put('t_no_cumple', 0);
        $result->put('pa_no_realizados', 0);
        $result->put('actividades_totales', 0);

        foreach ($consultas as $key => $value)
        {
          $result->put('inspections', $result->get('inspections') + $value->numero_inspecciones);
          $result->put('numero_items', $result->get('numero_items') + $value->numero_items);
          $result->put('t_cumple', $result->get('t_cumple') + $value->numero_items_cumplimiento);
          $result->put('t_no_cumple', $result->get('t_no_cumple') + $value->numero_items_no_cumplimiento);
          $result->put('pa_no_realizados', $result->get('pa_no_realizados') + $value->numero_planes_no_ejecutados);
          $result->put('actividades_totales', $result->get('actividades_totales') + $value->actividades_totales);
        }

        if ($result->get('numero_items') > 0)
          $result->put('p_cumple', round(($result->get('t_cumple') / $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_cumple', '0%');

        if ($result->get('numero_items') > 0)
          $result->put('p_no_cumple', round(($result->get('t_no_cumple')/ $result->get('numero_items')) * 100, 1)."%");
        else
          $result->put('p_no_cumple', '0%');

        $result->put('pa_realizados', $result->get('actividades_totales') - $result->get('pa_no_realizados'));
    
        return $this->respondHttp200([
            'data' => $result
        ]);
    }

    public function export(Request $request)
    {
      try
        {
          $headquarters = $this->getValuesForMultiselect($request->headquarters);
          $areas = $this->getValuesForMultiselect($request->areas);
          $themes = $this->getValuesForMultiselect($request->themes);
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
                'headquarters' => $headquarters,
                'areas' => $areas,
                'themes' => $themes,
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

      $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
      $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
      
      $themes = !$init ? $this->getValuesForMultiselect($request->themes) : (isset($filters['themes']) ? $this->getValuesForMultiselect($filters['themes']) : []);

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

      $informManager = new InformManagerInspections($this->company, $headquarters, $areas, $themes, $filtersType, $dates);

      return $this->respondHttp200($informManager->getInformData());
    }

    public function multiselectBar()
    {
      $keywords = $this->user->getKeywords();

      $select = [
          'Inspecciones' => "inspection", 
          'Temas' => "theme",
          $keywords['headquarters'] => "headquarter",
          $keywords['areas'] => "area"

      ];
  
      return $this->multiSelectFormat(collect($select));
    }
}