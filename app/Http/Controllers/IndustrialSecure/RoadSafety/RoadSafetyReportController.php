<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverInfraction;
use App\Models\IndustrialSecure\RoadSafety\Vehicle;
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
                'datesM' => $datesMaintenance, 
                'datesC' => $datesCombustible,
                'filtersType' => $filtersType,
                'table' => $request->table
            ];

            InspectionReportExportJob::dispatch($this->user, $this->company, $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function driverDocument(Request $request)
    {
        $documentsDriver = Driver::selectRaw("
                sau_rs_position_documents.id AS id,
                sau_employees.id,
                sau_employees_positions.name AS position,
                sau_employees.name AS driver,
                sau_employees.identification AS identification,
                sau_rs_position_documents.name AS document,
                case when sau_rs_drivers_documents.driver_id is not null then 'SI' else 'NO' end AS cargado_documento_driver,
                case when sau_rs_drivers_documents.expiration_date is not null  
                    then 
                        case when sau_rs_drivers_documents.expiration_date >= curdate()
                            then 'NO'
                            else 'SI' end
                    else 'NO' end AS vencido_documento_driver
            ")
            ->join('sau_employees','sau_employees.id', 'sau_rs_drivers.employee_id')
            ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
            ->join('sau_rs_positions', 'sau_rs_positions.employee_position_id', 'sau_employees_positions.id')
            ->join('sau_rs_position_documents', 'sau_rs_position_documents.position_id', 'sau_rs_positions.id')
            ->leftJoin('sau_rs_drivers_documents', function ($join) 
            {
                $join->on("sau_rs_drivers_documents.driver_id", "sau_rs_drivers.id");
                $join->on("sau_rs_drivers_documents.position_document_id", "sau_rs_position_documents.id");
            })
            ->where('sau_employees.company_id', $this->company)
            ->groupBy('sau_employees.id', 'sau_rs_position_documents.id', 'sau_rs_drivers_documents.driver_id', 'sau_rs_drivers_documents.expiration_date');
            
        $url = "/industrialsecure/roadsafety/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["regionals"]))
                $documentsDriver->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $documentsDriver->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $documentsDriver->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $documentsDriver->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            if (isset($filters["drivers"]))
                $documentsDriver->inDrivers($this->getValuesForMultiselect($filters["drivers"]), $filters['filtersType']['drivers']);
        }

        return Vuetable::of($documentsDriver)
                    ->make();
    }

    public function vehiclesDocument(Request $request)
    {
        $documentsVehicles = Vehicle::selectRaw("
                sau_rs_vehicles.id AS id,  
                sau_rs_vehicles_types.name AS type_vehicle, 
                sau_rs_vehicles.plate AS plate,  
                group_concat(sau_employees.name) AS driver,
                sau_employees_regionals.name AS regional,
                sau_employees_headquarters.name AS headquarter,
                sau_employees_processes.name AS process,
                sau_employees_areas.name AS area,
                case when sau_rs_vehicles.due_date_soat is not null  
                    then 
                        case when sau_rs_vehicles.due_date_soat >= curdate()
                            then 'NO'
                            else 'SI' end
                    else 'NO' end as report_vehicle_soat_vencido,
                case when sau_rs_vehicles.due_date_mechanical_tech is not null  
                    then 
                        case when sau_rs_vehicles.due_date_mechanical_tech >= curdate()
                            then 'NO'
                            else 'SI' end
                    else 'NO' end as report_vehicle_mechanical_vencido,
                case when sau_rs_vehicles.due_date_policy is not null  
                    then 
                        case when sau_rs_vehicles.due_date_policy >= curdate()
                            then 'NO'
                            else 'SI' end
                    else 'NO' end as report_vehicle_policy_vencido
            ")
            ->join('sau_rs_vehicles_types', 'sau_rs_vehicles_types.id', 'sau_rs_vehicles.type_vehicle')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rs_vehicles.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rs_vehicles.employee_headquarter_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rs_vehicles.employee_area_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rs_vehicles.employee_process_id')
            ->leftJoin('sau_rs_driver_vehicles', 'sau_rs_driver_vehicles.vehicle_id', 'sau_rs_vehicles.id')
            ->leftJoin('sau_rs_drivers', 'sau_rs_drivers.id', 'sau_rs_driver_vehicles.driver_id')
            ->leftJoin('sau_employees','sau_employees.id', 'sau_rs_drivers.employee_id')
            ->where('sau_rs_vehicles.company_id', $this->company)
            ->groupBy('sau_rs_vehicles.id');

            $url = "/industrialsecure/roadsafety/report";

            $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

            if (COUNT($filters) > 0)
            {    
                if (isset($filters["regionals"]))
                    $documentsVehicles->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);
    
                if (isset($filters["headquarters"]))
                    $documentsVehicles->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
    
                if (isset($filters["processes"]))
                    $documentsVehicles->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
                
                if (isset($filters["areas"]))
                    $documentsVehicles->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
                    
                if (isset($filters["drivers"]))
                    $documentsVehicles->inDrivers($this->getValuesForMultiselect($filters["drivers"]), $filters['filtersType']['drivers']);
                    
                if (isset($filters["vehicles"]))
                    $documentsVehicles->inVehicles($this->getValuesForMultiselect($filters["vehicles"]), $filters['filtersType']['vehicles']);
            }

        return Vuetable::of($documentsVehicles)
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

        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);

        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);

        $drivers = !$init ? $this->getValuesForMultiselect($request->drivers) : (isset($filters['drivers']) ? $this->getValuesForMultiselect($filters['drivers']) : []);

        $vehicles = !$init ? $this->getValuesForMultiselect($request->vehicles) : (isset($filters['vehicles']) ? $this->getValuesForMultiselect($filters['vehicles']) : []);

        $filtersType =  !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $datesMaintenance = [];

        $datesM = !$init ? $request->dateRangeMaintenance : (isset($filters['dateRangeMaintenance']) ? $filters['dateRangeMaintenance'] : null);

        if (isset($datesM) && $datesM)
        {
            $dates_request_m = explode('/', $datesM);

            if (COUNT($dates_request_m) == 2)
            {
                array_push($datesMaintenance, (Carbon::createFromFormat('D M d Y',$dates_request_m[0]))->format('Y-m-d'));
                array_push($datesMaintenance, (Carbon::createFromFormat('D M d Y',$dates_request_m[1]))->format('Y-m-d'));
            }
        }

        $datesCombustible = [];

        $datesC = !$init ? $request->dateRangeCombustible : (isset($filters['dateRangeCombustible']) ? $filters['dateRangeCombustible'] : null);

        if (isset($datesC) && $datesC)
        {
            $dates_request_c = explode('/', $datesC);

            if (COUNT($dates_request_c) == 2)
            {
                array_push($datesCombustible, (Carbon::createFromFormat('D M d Y',$dates_request_c[0]))->format('Y-m-d'));
                array_push($datesCombustible, (Carbon::createFromFormat('D M d Y',$dates_request_c[1]))->format('Y-m-d'));
            }
        }

        $informData = collect([]);

        $informData->put('driverInfractions', $this->reportDriverInfractions($regionals, $headquarters, $processes, $areas, $filtersType, $drivers));
        $informData->put('reportMaintenancePlate', $this->reportMaintenancePlate($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles));
        $informData->put('reportMaintenanceYear', $this->reportMaintenanceYear($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles));
        $informData->put('reportMaintenanceMonth', $this->reportMaintenanceMonth($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles));
        $informData->put('reportMaintenanceType', $this->reportMaintenanceType($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles));
        $informData->put('reporCombustiblePlate', $this->reporCombustiblePlate($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles));
        $informData->put('reportCombustibleYear', $this->reportCombustibleYear($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles));
        $informData->put('reportCombustibleMonth', $this->reportCombustibleMonth($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles));
        $informData->put('reportCombustibleCost', $this->reportCombustibleCost($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles));
        $informData->put('selectBar', $this->multiselectBar());
        

        return $this->respondHttp200($informData->toArray());
    }

    public function multiselectBar()
    {
      $select = [
          'Placas' => "reporCombustiblePlate", 
          'Mes' => "reportCombustibleMonth",
          'Año' => "reportCombustibleYear",
          'Costo' => "reportCombustibleCost"
      ];
  
      return $this->multiSelectFormat(collect($select));
    }

    public function reportDriverInfractions($regionals, $headquarters, $processes, $areas, $filtersType, $drivers)
    {
      $consultas = Driver::select(
            "sau_employees.name as category", 
            DB::raw('COUNT(DISTINCT sau_rs_driver_infractions.id) as count')
      )
      ->join('sau_employees','sau_employees.id', 'sau_rs_drivers.employee_id')
      ->join('sau_rs_driver_infractions','sau_rs_driver_infractions.driver_id', 'sau_rs_drivers.id')
      ->where('sau_employees.company_id', $this->company)
      ->groupBy('sau_rs_drivers.id');

        if (COUNT($regionals) > 0)
            $consultas->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $consultas->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $consultas->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $consultas->inAreas($areas, $filtersType['areas']);

        if (COUNT($drivers) > 0)
            $consultas->inDrivers($drivers, $filtersType['drivers']);

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

    private function reportMaintenancePlate($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles)
    {
        $checksPerPlate = Vehicle::selectRaw("
            sau_rs_vehicles.plate,
            COUNT(vehicle_id) AS count_per_plate
        ")
        ->join('sau_rs_vehicle_maintenance', 'sau_rs_vehicle_maintenance.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('plate');


        if (COUNT($regionals) > 0)
            $checksPerPlate->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerPlate->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerPlate->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerPlate->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerPlate->inVehicles($vehicles, $filtersType['vehicles']);

        $checksPerPlate = $checksPerPlate->pluck('count_per_plate', 'plate');

        return $this->buildDataChart($checksPerPlate);
    }

    public function reportMaintenanceYear($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles)
    {
        $checksPerYear = Vehicle::selectRaw("
            YEAR(sau_rs_vehicle_maintenance.date) AS year,
            COUNT(vehicle_id) AS count_per_year
        ")
        ->join('sau_rs_vehicle_maintenance', 'sau_rs_vehicle_maintenance.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('year');

        if (COUNT($regionals) > 0)
            $checksPerYear->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerYear->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerYear->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerYear->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerYear->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesMaintenance) > 0)
            $checksPerYear->betweenMaintenance($datesMaintenance);

        $checksPerYear = $checksPerYear->pluck('count_per_year', 'year');

        return $this->buildDataChart($checksPerYear);
    }

    public function reportMaintenanceMonth($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles)
    {
        $checksPerMonth = Vehicle::selectRaw("
            MONTH(sau_rs_vehicle_maintenance.date) AS month,
            COUNT(vehicle_id) AS count_per_month
        ")
        ->join('sau_rs_vehicle_maintenance', 'sau_rs_vehicle_maintenance.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('month');        

        if (COUNT($regionals) > 0)
            $checksPerMonth->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerMonth->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerMonth->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerMonth->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerMonth->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesMaintenance) > 0)
            $checksPerMonth->betweenMaintenance($datesMaintenance);

        $checksPerMonth = $checksPerMonth->pluck('count_per_month', 'month');

        $months = [];
        $data = [];
        $total = 0;

        for ($i = 1; $i <= 12; $i++)
        {
            array_push($months, trans("months.$i"));
            $value = isset($checksPerMonth[$i]) ? $checksPerMonth[$i] : 0;
            array_push($data, $value);
            $total += $value;
        }

        return collect([
            'labels' => $months,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
    }

    public function reportMaintenanceType($regionals, $headquarters, $processes, $areas, $filtersType, $datesMaintenance, $vehicles)
    {
        $checksPerType = Vehicle::selectRaw("
            sau_rs_vehicle_maintenance.type AS type,
            COUNT(vehicle_id) AS count_per_type
        ")
        ->join('sau_rs_vehicle_maintenance', 'sau_rs_vehicle_maintenance.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('type');

        if (COUNT($regionals) > 0)
            $checksPerType->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerType->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerType->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerType->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerType->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesMaintenance) > 0)
            $checksPerType->betweenMaintenance($datesMaintenance);

        $checksPerType = $checksPerType->pluck('count_per_type', 'type');

        return $this->buildDataChart($checksPerType);
    }
    
    private function reporCombustiblePlate($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles)
    {
        $checksPerPlate = Vehicle::selectRaw("
            sau_rs_vehicles.plate,
            SUM(sau_rs_vehicle_combustibles.quantity_galons) AS count_per_plate
        ")
        ->join('sau_rs_vehicle_combustibles', 'sau_rs_vehicle_combustibles.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('plate');

        if (COUNT($regionals) > 0)
            $checksPerPlate->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerPlate->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerPlate->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerPlate->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerPlate->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesCombustible) > 0)
            $checksPerPlate->betweenCombustible($datesCombustible);

        $checksPerPlate = $checksPerPlate->pluck('count_per_plate', 'plate');

        return $this->buildDataChart($checksPerPlate);
    }

    public function reportCombustibleCost($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles)
    {
        $checksPerYear = Vehicle::selectRaw("
            sau_rs_vehicle_combustibles.price_galon AS cost,
            SUM(sau_rs_vehicle_combustibles.quantity_galons) AS count_per_cost
        ")
        ->join('sau_rs_vehicle_combustibles', 'sau_rs_vehicle_combustibles.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('cost');

        if (COUNT($regionals) > 0)
            $checksPerYear->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerYear->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerYear->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerYear->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerYear->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesCombustible) > 0)
            $checksPerYear->betweenCombustible($datesCombustible);

        $checksPerYear = $checksPerYear->pluck('count_per_cost', 'cost');

        return $this->buildDataChart($checksPerYear);
    }

    public function reportCombustibleYear($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles)
    {
        $checksPerYear = Vehicle::selectRaw("
            YEAR(sau_rs_vehicle_combustibles.date) AS year,
            SUM(sau_rs_vehicle_combustibles.quantity_galons) AS count_per_year
        ")
        ->join('sau_rs_vehicle_combustibles', 'sau_rs_vehicle_combustibles.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('year');

        if (COUNT($regionals) > 0)
            $checksPerYear->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerYear->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerYear->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerYear->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerYear->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesCombustible) > 0)
            $checksPerYear->betweenCombustible($datesCombustible);

        $checksPerYear = $checksPerYear->pluck('count_per_year', 'year');

        return $this->buildDataChart($checksPerYear);
    }

    public function reportCombustibleMonth($regionals, $headquarters, $processes, $areas, $filtersType, $datesCombustible, $vehicles)
    {
        $checksPerMonth = Vehicle::selectRaw("
            MONTH(sau_rs_vehicle_combustibles.date) AS month,
            SUM(sau_rs_vehicle_combustibles.quantity_galons) AS count_per_month
        ")
        ->join('sau_rs_vehicle_combustibles', 'sau_rs_vehicle_combustibles.vehicle_id', 'sau_rs_vehicles.id')
        ->groupBy('month');

        if (COUNT($regionals) > 0)
            $checksPerMonth->inRegionals($regionals, $filtersType['regionals']);

        if (COUNT($headquarters) > 0)
            $checksPerMonth->inHeadquarters($headquarters, $filtersType['headquarters']);

        if (COUNT($processes) > 0)
            $checksPerMonth->inProcesses($processes, $filtersType['processes']);

        if (COUNT($areas) > 0)
            $checksPerMonth->inAreas($areas, $filtersType['areas']);

        if (COUNT($vehicles) > 0)
            $checksPerMonth->inVehicles($vehicles, $filtersType['vehicles']);

        if (COUNT($datesCombustible) > 0)
            $checksPerMonth->betweenCombustible($datesCombustible);

        $checksPerMonth = $checksPerMonth->pluck('count_per_month', 'month');

        $months = [];
        $data = [];
        $total = 0;

        for ($i = 1; $i <= 12; $i++)
        {
            array_push($months, trans("months.$i"));
            $value = isset($checksPerMonth[$i]) ? $checksPerMonth[$i] : 0;
            array_push($data, $value);
            $total += $value;
        }

        return collect([
            'labels' => $months,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
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