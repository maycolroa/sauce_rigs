<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections\InspectionRequest;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionExportJob;
use Carbon\Carbon;
use App\Traits\Filtertrait;
use DB;

class InspectionController extends Controller
{
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    { 
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:ph_inspections_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:ph_inspections_r, {$this->team}");
        $this->middleware("permission:ph_inspections_u, {$this->team}", ['only' => ['update', 'toggleState']]);
        $this->middleware("permission:ph_inspections_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:ph_inspections_export, {$this->team}", ['only' => 'export']);
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
        $inspections = Inspection::select(
                'sau_ph_inspections.*',
                DB::raw('GROUP_CONCAT(DISTINCT sau_employees_regionals.name ORDER BY sau_employees_regionals.name ASC) AS regional'),
                DB::raw('GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS headquarter'),
                DB::raw('GROUP_CONCAT(DISTINCT sau_employees_processes.name ORDER BY sau_employees_processes.name ASC) AS process'),
                DB::raw('GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area')
            )
            ->leftJoin('sau_ph_inspection_regional', 'sau_ph_inspection_regional.inspection_id', 'sau_ph_inspections.id')
            ->leftJoin('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
            ->leftJoin('sau_ph_inspection_process', 'sau_ph_inspection_process.inspection_id', 'sau_ph_inspections.id')
            ->leftJoin('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_regional.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_process.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
            ->groupBy('sau_ph_inspections.id', 'sau_ph_inspections.name');

        $url = "/industrialsecure/dangerousconditions/inspections";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $inspections->inInspections($this->getValuesForMultiselect($filters["inspections"]), $filters['filtersType']['inspections']);

            if (isset($filters["regionals"]))
                $inspections->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $inspections->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $inspections->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $inspections->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
            
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $inspections->betweenDate($dates);
        }

        return Vuetable::of($inspections)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InspectionRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $inspection = new Inspection($request->all());
            $inspection->company_id = $this->company;
            
            if (!$inspection->save())
                return $this->respondHttp500();

            $this->saveLocation($inspection, $request);
            
            $this->saveAdditionalFields($inspection, $request->get('additional_fields'));

            $this->saveThemes($inspection, $request->get('themes'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la inspección'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $inspection = Inspection::findOrFail($id);
            $regionals = [];
            $headquarters = [];
            $processes = [];
            $areas = [];

            foreach ($inspection->regionals as $key => $value)
            {
                array_push($regionals, $value->multiselect());
            }

            foreach ($inspection->headquarters as $key => $value)
            {
                array_push($headquarters, $value->multiselect());
            }

            foreach ($inspection->processes as $key => $value)
            {
                array_push($processes, $value->multiselect());
            }

            foreach ($inspection->areas as $key => $value)
            {
                array_push($areas, $value->multiselect());
            }

            $inspection->multiselect_regional = $regionals;
            $inspection->employee_regional_id = $regionals;

            $inspection->multiselect_sede = $headquarters;
            $inspection->employee_headquarter_id = $headquarters;

            $inspection->multiselect_proceso = $processes;
            $inspection->employee_process_id = $processes;

            $inspection->multiselect_area = $areas;
            $inspection->employee_area_id = $areas;

            foreach ($inspection->additional_fields as $field)
            {
                $field->key = Carbon::now()->timestamp + rand(1,10000);
            }

            foreach ($inspection->themes as $theme)
            {
                $theme->key = Carbon::now()->timestamp + rand(1,10000);

                foreach ($theme->items as $item)
                {
                    $item->key = Carbon::now()->timestamp + rand(1,10000);
                }
            }

            $inspection->delete = [
                'additional_fields' => [],
                'themes' => [],
                'items' => []
            ];

            return $this->respondHttp200([
                'data' => $inspection,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @param  Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function update(InspectionRequest $request, Inspection $inspection)
    {
        DB::beginTransaction();

        try
        {
            $inspection->fill($request->all());
            
            if (!$inspection->update())
                return $this->respondHttp500();

            $this->saveLocation($inspection, $request);
            
            $this->saveAdditionalFields($inspection, $request->get('additional_fields'));

            $this->saveThemes($inspection, $request->get('themes'));

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            //\Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la inspección'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Inspection $inspection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inspection $inspection)
    {
        
    }

    public function saveAdditionalFields($inspection, $additional_fields)
    {
        foreach ($additional_fields as $field)
        {
            $id = isset($field['id']) ? $field['id'] : NULL;
            $fieldNew = $inspection->additional_fields()->updateOrCreate(['id'=>$id], $field);
        }
    }    

    private function saveThemes($inspection, $themes)
    {
        foreach ($themes as $theme)
        {
            $id = isset($theme['id']) ? $theme['id'] : NULL;
            $themeNew = $inspection->themes()->updateOrCreate(['id'=>$id], $theme);

            $this->saveItems($themeNew, $theme['items']);
        }
    }

    private function saveItems($theme, $items)
    {
        foreach ($items as $item)
        {
            $id = isset($item['id']) ? $item['id'] : NULL;
            $itemNew = $theme->items()->updateOrCreate(['id'=>$id], $item);
        }
    }

    private function saveLocation($inspection, $request)
    {
        $regionals = [];
        $headquarters = [];
        $processes = [];
        $areas = [];

        if ($request->has('employee_regional_id'))
            $regionals = $this->getDataFromMultiselect($request->get('employee_regional_id'));
        
        if ($request->has('employee_headquarter_id'))
            $headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));

        if ($request->has('employee_process_id'))
            $processes = $this->getDataFromMultiselect($request->get('employee_process_id'));

        if ($request->has('employee_area_id'))
            $areas = $this->getDataFromMultiselect($request->get('employee_area_id'));

        $headquarters_valid = $this->getHeadquarter($regionals);
        $headquarters = array_intersect($headquarters, $headquarters_valid);

        $processes_valid = $this->getProcess($headquarters);
        $processes = array_intersect($processes, $processes_valid);

        $areas_valid = $this->getAreas($headquarters, $processes);
        $areas = array_intersect($areas, $areas_valid);

        $inspection->headquarters()->sync($headquarters);
        $inspection->regionals()->sync($regionals);
        $inspection->processes()->sync($processes);
        $inspection->areas()->sync($areas);
    }

    private function deleteData($data)
    {    
        if (COUNT($data['themes']) > 0)
            InspectionSection::destroy($data['themes']);

        if (COUNT($data['items']) > 0)
            InspectionSectionItem::destroy($data['items']);

        if (COUNT($data['additional_fields']) > 0)
        AdditionalFields::destroy($data['additional_fields']);
    }

    /**
     * toggles the check state between open and close
     * @param  Inspection $inspection
     * @return \Illuminate\Http\Response
     */
    public function toggleState(Inspection $inspection)
    {
        $newState = $inspection->isActive() ? "NO" : "SI";
        $data = ['state' => $newState];

        if (!$inspection->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado de la inspección'
        ]);
    }

    private function getHeadquarter($regionals)
    {
        $headquarters = EmployeeHeadquarter::selectRaw(
            "sau_employees_headquarters.id as id")
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->whereIn('employee_regional_id', $regionals)
        ->pluck('id')
        ->toArray();

        return $headquarters;
    }

    private function getProcess($headquarters)
    {
        $processes = EmployeeProcess::selectRaw(
            "sau_employees_processes.id as id")
        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->whereIn('sau_headquarter_process.employee_headquarter_id', $headquarters)
        ->pluck('id')
        ->toArray();

        return $processes;
    }

    private function getAreas($headquarters, $process)
    {
        $areas = EmployeeArea::selectRaw(
            "sau_employees_areas.id as id")
        ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
        ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
        ->whereIn('employee_headquarter_id', $headquarters)
        ->whereIn('employee_process_id', $process)
        ->pluck('id')
        ->toArray();
    
        return $areas;
    }

    public function export(Request $request)
    {
      try
      {
        $inspections = $this->getValuesForMultiselect($request->inspections);
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $headquarters = $this->getValuesForMultiselect($request->headquarters);
        $processes = $this->getValuesForMultiselect($request->processes);
        $areas = $this->getValuesForMultiselect($request->areas);
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }

        $filters = [
            'inspections' => $inspections,
            'regionals' => $regionals,
            'headquarters' => $headquarters,
            'processes' => $processes,
            'areas' => $areas,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        InspectionExportJob::dispatch($this->user, $this->company, $filters);
      
        return $this->respondHttp200();

      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }

    public function multiselectThemes()
    {
        $themes = Inspection::selectRaw(
          "GROUP_CONCAT(sau_ph_inspection_sections.id) as ids,
          sau_ph_inspection_sections.name as name")
        ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
        ->groupBy('sau_ph_inspection_sections.name')
        ->pluck('ids', 'name');

      return $this->multiSelectFormat($themes);
    }

    public function multiselectInspection()
    {
        $inspections = Inspection::select(
          "sau_ph_inspections.id as id",
          "sau_ph_inspections.name as name"
        )
        ->orderBy('name')
        ->pluck('id', 'name');

      return $this->multiSelectFormat($inspections);
    }
}
