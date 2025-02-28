<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverInfraction;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use App\Traits\LocationFormTrait;
use Carbon\Carbon;
use Validator;
use DB;

class RoadSafetyReportController extends Controller
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
       // $this->middleware("permission:ph_inspections_r, {$this->team}");
        //$this->middleware('permission:ph_inspections_u', ['only' => 'update']);
        //$this->middleware('permission:ph_inspections_d', ['only' => 'destroy']);
       // $this->middleware("permission:ph_inspections_report_export, {$this->team}", ['only' => 'export']);
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

    public function reportDriverInfractions($regionals, $headquarters, $processes, $areas, $filtersType, $dates, $drivers)
    {
      $consultas = Driver::select(
            "sau_employees.name as category", 
            DB::raw('COUNT(DISTINCT sau_rs_driver_infractions.id) as count')
      )
      ->join('sau_employees','sau_employees.id', 'sau_rs_drivers.employee_id')
      ->join('sau_rs_driver_infractions','sau_rs_driver_infractions.driver_id', 'sau_rs_drivers.id')
      ->where('sau_employees.company_id', $this->company)
      ->groupBy('sau_rs_drivers.id');

        /*if ($this->locationLevelForm == 'Regional' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter);
        }
        else if ($this->locationLevelForm == 'Sede' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter);
        }
        else if ($this->locationLevelForm == 'Proceso' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter);
        }
        else if ($this->locationLevelForm == 'Área' && COUNT($this->regionalsFilter) > 0)
        {
            $consultas->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $this->regionalsFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $this->headquartersFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $this->processesFilter)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $this->areasFilter);
        }

        if (COUNT($this->regionals) > 0)
            $consultas->inRegionals($this->regionals, $this->filtersType['regionals']);

        if (COUNT($this->headquarters) > 0)
            $consultas->inHeadquarters($this->headquarters, $this->filtersType['headquarters']);

        if (COUNT($this->processes) > 0)
            $consultas->inProcesses($this->processes, $this->filtersType['processes']);

        if (COUNT($this->areas) > 0)
            $consultas->inAreas($this->areas, $this->filtersType['areas']);*/

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart($consultas);
    }

    public function driverDocument(Request $request)
    {
        $documentsEmployee = Driver::selectRaw("
                sau_ct_information_contract_lessee.id AS id,
                sau_ct_information_contract_lessee.social_reason AS contract,
                sau_ct_contract_employees.name AS employee,
                sau_ct_activities.name As activity,
                sau_ct_activities_documents.name AS document,
                case when sau_ct_file_document_employee.employee_id is not null then 'SI' else 'NO' end as cargado
            ")
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->join('sau_ct_activities_documents', 'sau_ct_activities_documents.activity_id', 'sau_ct_activities.id')
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            });

        
        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $contract_user_id = $this->getContractIdUser($this->user->id);

            $documentsEmployee->where('sau_ct_information_contract_lessee.id', $contract_user_id);
        }
            
        $url = "/legalaspects/report/contracts";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $documentsEmployee->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsEmployee->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function reportDinamic(Request $request)
    {
        $url = "/industrialsecure/roadsafety/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $regionals = [];//!$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

        $qualifiers = [];// !$init ? $this->getValuesForMultiselect($request->qualifiers) : (isset($filters['qualifiers']) ? $this->getValuesForMultiselect($filters['qualifiers']) : []);

        $headquarters = [];// !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);

        $processes = [];// !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $areas = [];// !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
        $themes = [];// !$init ? $this->getValuesForMultiselect($request->themes) : (isset($filters['themes']) ? $this->getValuesForMultiselect($filters['themes']) : []);

        $drivers = [];// !$init ? $this->getValuesForMultiselect($request->drivers) : (isset($filters['drivers']) ? $this->getValuesForMultiselect($filters['drivers']) : []);

        $filtersType =  [];//!$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $dates = [];

        $datesF =  [];//!$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        /*if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }
        }
*/
        $informData = collect([]);

        $informData->put('driverInfractions', $this->reportDriverInfractions($regionals, $headquarters, $processes, $areas, $filtersType, $dates, $drivers));

        return $this->respondHttp200($informData->toArray());
    }

    protected function buildDataChart($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $label => $count) {
            $label2 = strlen($label) > 50 ? substr($this->sanear_string($label), 0, 50).'...' : $label;
            array_push($labels, $label2);
            array_push($data, ['name' => $label, 'value' => $count]);
            $total += $count;
        }

        return collect([
            'labels' => $labels,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
    }
}