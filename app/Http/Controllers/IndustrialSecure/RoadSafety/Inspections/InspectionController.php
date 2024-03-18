<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\TypeInspections;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\TypeInspectionsItems;
use App\Models\IndustrialSecure\RoadSafety\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSection;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSectionItem;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Http\Requests\IndustrialSecure\RoadSafety\Inspections\InspectionRequest;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionExportJob;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionImportTemplateExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionImportJob;
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
        /*$this->middleware("permission:roadSafety_inspections_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:roadSafety_inspections_r, {$this->team}");
        $this->middleware("permission:roadSafety_inspections_u, {$this->team}", ['only' => ['update', 'toggleState']]);
        $this->middleware("permission:roadSafety_inspections_d, {$this->team}", ['only' => 'destroy']);
        //$this->middleware("permission:roadSafety_inspections_export, {$this->team}", ['only' => 'export']);*/
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
        $confLocationTableInspections = $this->getLocationFormConfTableInspections();

        $select = [];
        $having = [];

        $select[] = "sau_rs_inspections.*";

        if ($confLocationTableInspections['regional'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(r.name) 
                FROM sau_rs_inspection_regional ri 
                INNER JOIN sau_employees_regionals r ON r.id = ri.employee_regional_id
                WHERE ri.inspection_id = sau_rs_inspections.id
            ) AS regional";

        if ($confLocationTableInspections['headquarter'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(h.name) 
                FROM sau_rs_inspection_headquarter hi 
                INNER JOIN sau_employees_headquarters h ON h.id = hi.employee_headquarter_id
                WHERE hi.inspection_id = sau_rs_inspections.id
            ) AS headquarter";

        if ($confLocationTableInspections['process'] == 'SI')
            $select[] = "(SELECT GROUP_CONCAT(p.name) 
                FROM sau_rs_inspection_process pi
                INNER JOIN sau_employees_processes p ON p.id = pi.employee_process_id
                WHERE pi.inspection_id = sau_rs_inspections.id
            ) AS procesos";

        if ($confLocationTableInspections['area'] == 'SI')
            $select[] = " (SELECT GROUP_CONCAT(ar.name) 
                FROM sau_rs_inspection_area a 
                INNER JOIN sau_employees_areas ar ON ar.id = a.employee_area_id
                WHERE a.inspection_id = sau_rs_inspections.id
            ) AS areas";


        $inspections = Inspection::groupBy('sau_rs_inspections.id', 'sau_rs_inspections.name')->orderBy('sau_rs_inspections.id', 'DESC');  

        $url = "/industrialsecure/roadSafety/inspections";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $inspections->inInspections($this->getValuesForMultiselect($filters["inspections"]), $filters['filtersType']['inspections']);

            if (isset($filters["regionals"]) && $filters["regionals"])   
            {         
                $select[] = "(SELECT COUNT(rf.employee_regional_id) 
                    FROM sau_rs_inspection_regional rf 
                    WHERE rf.inspection_id = sau_rs_inspections.id
                    and rf.employee_regional_id ".$filters['filtersType']['regionals']." (".implode(',', $this->getValuesForMultiselect($filters["regionals"])->toArray()).")
                ) AS regional2";    

                $having[] = "regional2 >= 1";
            }

            if (isset($filters["headquarters"]) && $filters["headquarters"])
            {
                $select[] = "(SELECT COUNT(hf.employee_headquarter_id) 
                    FROM sau_rs_inspection_headquarter hf 
                    WHERE hf.inspection_id = sau_rs_inspections.id
                    and hf.employee_headquarter_id ".$filters['filtersType']['headquarters']." (".implode(',', $this->getValuesForMultiselect($filters["headquarters"])->toArray()).")
                ) AS headquarter2";    

                $having[] = "headquarter2 >= 1";
            }

            if (isset($filters["processes"]) && $filters["processes"])
            {
                $select[] = "(SELECT COUNT(pf.employee_process_id) 
                    FROM sau_rs_inspection_process pf 
                    WHERE pf.inspection_id = sau_rs_inspections.id
                    and pf.employee_process_id ".$filters['filtersType']['processes']." (".implode(',', $this->getValuesForMultiselect($filters["processes"])->toArray()).")
                ) AS procesos2";    

                $having[] = "procesos2 >= 1";
            }
            
            if (isset($filters["areas"]) && $filters["areas"])
            {
                $select[] = "(SELECT COUNT(af.employee_area_id) 
                    FROM sau_rs_inspection_area af 
                    WHERE af.inspection_id = sau_rs_inspections.id
                    and af.employee_area_id  ".$filters['filtersType']['areas']." (".implode(',', $this->getValuesForMultiselect($filters["areas"])->toArray()).")
                ) AS areas2"; 

                $having[] = "areas2 >= 1";
            }

            if (COUNT($having) > 0)
                $inspections->havingRaw(implode(" and ", $having));
            
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $inspections->betweenDate($dates);

        }

        $inspections->selectRaw(implode(",", $select));
        
        /*try
        {
            $configLevel = ConfigurationsCompany::company($this->company)->findByKey('filter_inspections');
        } catch (\Exception $e) {
            $configLevel = 'NO';
        }

        if ($configLevel == 'SI')
        {
            $locationLevelForm = ConfigurationsCompany::company($this->company)->findByKey('location_level_form_user_inspection_filter');

            if ($locationLevelForm)
            {
                if ($locationLevelForm == 'Regional')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');

                    if (count($regionals) > 0)
                        $inspections->join('sau_rs_inspection_regional', 'sau_rs_inspection_regional.inspection_id', 'sau_rs_inspections.id')
                        ->whereIn('sau_rs_inspection_regional.employee_regional_id',$regionals);
                }
                else if ($locationLevelForm == 'Sede')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0)
                        $inspections->join('sau_rs_inspection_regional', 'sau_rs_inspection_regional.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_headquarter', 'sau_rs_inspection_headquarter.inspection_id', 'sau_rs_inspections.id')
                        ->whereIn('sau_rs_inspection_headquarter.employee_headquarter_id',$headquarters)
                        ->whereIn('sau_rs_inspection_regional.employee_regional_id', $regionals);
                }
                else if ($locationLevelForm == 'Proceso')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                    $processes = User::find($this->user->id)->processes()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0)
                        $inspections->join('sau_rs_inspection_regional', 'sau_rs_inspection_regional.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_headquarter', 'sau_rs_inspection_headquarter.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_process', 'sau_rs_inspection_process.inspection_id', 'sau_rs_inspections.id')
                        ->whereIn('sau_rs_inspection_regional.employee_regional_id',$regionals)->whereIn('sau_rs_inspection_headquarter.employee_headquarter_id',$headquarters)
                        ->whereIn('sau_rs_inspection_process.employee_process_id',$processes);
                }
                else if ($locationLevelForm == 'Área')
                {                        
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                    $processes = User::find($this->user->id)->processes()->pluck('id');
                    $areas = User::find($this->user->id)->areas()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0 && count($areas) > 0)
                    {
                        $inspections->join('sau_rs_inspection_regional', 'sau_rs_inspection_regional.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_headquarter', 'sau_rs_inspection_headquarter.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_process', 'sau_rs_inspection_process.inspection_id', 'sau_rs_inspections.id')
                        ->join('sau_rs_inspection_area', 'sau_rs_inspection_area.inspection_id', 'sau_rs_inspections.id')
                        ->whereIn('sau_rs_inspection_regional.employee_regional_id',$regionals)->whereIn('sau_rs_inspection_headquarter.employee_headquarter_id',$headquarters)
                        ->whereIn('sau_rs_inspection_process.employee_process_id',$processes)
                        ->whereIn('sau_rs_inspection_area.employee_area_id',$areas);
                    }
                }
            }
        }*/

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

            $themes = $request->get('themes');

            $porcentage_total_theme = [];
            $porcentage_partial_theme = [];

            if($request->type_id == 2)
            {
                foreach ($themes as $key => $theme) 
                {
                    $porcentage_total_theme[$key] = 0;
                    $porcentage_partial_theme[$key] = 0;

                    foreach ($theme['items'] as $key2 => $item) 
                    {
                        $porcentage_total_theme[$key] = $porcentage_total_theme[$key] + $item['compliance_value'];
                        $porcentage_partial_theme[$key] = $porcentage_partial_theme[$key] + $item['partial_value'];
                    }

                    if ($porcentage_total_theme[$key] > 100 || $porcentage_partial_theme[$key] > 100)
                    {
                        return $this->respondWithError('El total del porcentaje total de cumplimiento o de cumplimiento parcial del tema  ' . $theme['name'] . ' es mayor a 100%');
                    }
                }
            }
            
            if (!$inspection->save())
                return $this->respondHttp500();

            $this->saveLocation($inspection, $request);
            $this->saveLogActivitySystem('Inspecciones Seguridad vial - Inspecciones planeadas', 'Se creo el formato de inspección '.$inspection->name);

            if ($request->has('additional_fields') && $request->additional_fields)            
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

                    if (isset($item->type_id) && $item->type_id != 4)
                        $item->values = $item->values ? implode("\n", $item->values->get('values')) : NULL;
                    else
                    {
                        $values = $item->values ? $item->values->get('values') : [];

                        if (count($values) > 0)
                        {
                            $item->min_value = $values[0];
                            $item->max_value = $values[1];
                        }
                    }
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

            $themes = $request->get('themes');

            $porcentage_total_theme = [];
            $porcentage_partial_theme = [];

            if($request->type_id == 2)
            {
                foreach ($themes as $key => $theme) 
                {
                    $porcentage_total_theme[$key] = 0;
                    $porcentage_partial_theme[$key] = 0;

                    foreach ($theme['items'] as $key2 => $item) 
                    {
                        $porcentage_total_theme[$key] = $porcentage_total_theme[$key] + $item['compliance_value'];
                        $porcentage_partial_theme[$key] = $porcentage_partial_theme[$key] + $item['partial_value'];
                    }

                    if ($porcentage_total_theme[$key] > 100 || $porcentage_partial_theme[$key] > 100)
                    {
                        return $this->respondWithError('El total del porcentaje total de cumplimiento o de cumplimiento parcial del tema  ' . $theme['name'] . ' es mayor a 100%');
                    }
                }
            }

            if (!$inspection->update())
                return $this->respondHttp500();
            
            $this->saveLocation($inspection, $request);

            if ($request->has('additional_fields') && $request->additional_fields)            
                $this->saveAdditionalFields($inspection, $request->get('additional_fields'));

            $this->saveThemes($inspection, $request->get('themes'));

            $this->deleteData($request->get('delete'));
            $this->saveLogActivitySystem('Inspecciones Seguridad vial - Inspecciones planeadas', 'Se edito el formato de inspección '.$inspection->name);

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
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
        DB::beginTransaction();

        try
        { 
            foreach ($inspection->themes as $theme)
            {
                foreach ($theme->items as $item)
                {
                    if ($item->qualifications->count() > 0)
                        return $this->respondWithError('No se puede eliminar la inspección porque hay inspecciones realizadas asociadas a ella');
                }
            }

            $this->saveLogDelete('Inspecciones Seguridad vial - Inspecciones planeadas', 'Se elimino el formato de inspección '.$inspection->name);

            if(!$inspection->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se elimino la inspección'
        ]);

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
            if (isset($item['min_value']) && isset($item['max_value']))
            {
                $values = [$item['min_value'],$item['max_value']];
                $config = collect(['values' => []]);
                $config->put('values', $values);
                \Log::info($config);

                $item['values'] = $config; 
            }
            else if (isset($item['values']))
            {
                $config = collect(['values' => []]);

                if (is_array($item['values']))
                    $config->put('values', explode("\n", $item['values'][0]));
                else 
                    $config->put('values', explode("\n", $item['values']));

                $item['values'] = $config;
            }

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

        $regional_alls = '';
        $headquarter_alls = '';
        $process_alls = '';
        $areas_alls = '';

        if ($request->has('employee_regional_id'))
        {
            if (count($request->employee_regional_id) > 1)
            {
                foreach ($request->employee_regional_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $regional_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_regional_id) == 1)
                $regional_alls =  json_decode($request->employee_regional_id[0])->value;
        }

        if ($request->has('employee_headquarter_id'))
        {
            if (count($request->employee_headquarter_id) > 1)
            {
                foreach ($request->employee_headquarter_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $headquarter_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_headquarter_id) == 1)
                $headquarter_alls =  json_decode($request->employee_headquarter_id[0])->value;
        }

        if ($request->has('employee_process_id'))
        {
            if (count($request->employee_process_id) > 1)
            {
                foreach ($request->employee_process_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $process_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_process_id) == 1)
                $process_alls =  json_decode($request->employee_process_id[0])->value;
        }

        if ($request->has('employee_area_id'))
        {
            if (count($request->employee_area_id) > 1)
            {
                foreach ($request->employee_area_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $areas_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_area_id) == 1)
                $areas_alls =  json_decode($request->employee_area_id[0])->value;
        }

        if ($request->has('employee_regional_id') && $regional_alls == 'Todos')
            $regionals = $this->getRegionals();

        else if ($request->has('employee_regional_id'))
            $regionals = $this->getDataFromMultiselect($request->get('employee_regional_id'));



        if ($request->has('employee_headquarter_id') && $headquarter_alls == 'Todos')
            $headquarters = $this->getHeadquarter($regionals);

        else if ($request->has('employee_headquarter_id'))
            $headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));



        if ($request->has('employee_process_id') && $process_alls == 'Todos')
            $processes = $this->getProcess($headquarters);

        else if ($request->has('employee_process_id'))
            $processes = $this->getDataFromMultiselect($request->get('employee_process_id'));



        if ($request->has('employee_area_id') && $areas_alls == 'Todos')
            $areas = $this->getAreas($headquarters, $processes);

        else if ($request->has('employee_area_id'))
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

    private function getRegionals()
    {
        $regionals = EmployeeRegional::selectRaw(
            "sau_employees_regionals.id as id")
        ->pluck('id')
        ->toArray();

        return $regionals;
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

    public function multiselectItems()
    {
        $items = Inspection::selectRaw(
          "GROUP_CONCAT(sau_rs_inspection_section_items.id) as ids,
          sau_rs_inspection_section_items.description as name")
        ->join('sau_rs_inspection_sections', 'sau_rs_inspection_sections.inspection_id', 'sau_rs_inspections.id')
        ->join('sau_rs_inspection_section_items', 'sau_rs_inspection_section_items.inspection_section_id', 'sau_rs_inspection_sections.id')
        ->groupBy('sau_rs_inspection_section_items.description')
        ->orderBy('name')
        ->pluck('ids', 'name');

      return $this->multiSelectFormat($items);
    }

    public function multiselectThemes()
    {
        $themes = Inspection::selectRaw(
          "GROUP_CONCAT(sau_rs_inspection_sections.id) as ids,
          sau_rs_inspection_sections.name as name")
        ->join('sau_rs_inspection_sections', 'sau_rs_inspection_sections.inspection_id', 'sau_rs_inspections.id')
        ->groupBy('sau_rs_inspection_sections.name')
        ->orderBy('name')
        ->pluck('ids', 'name');

      return $this->multiSelectFormat($themes);
    }

    public function multiselectInspection()
    {
        $inspections = Inspection::select(
          "sau_rs_inspections.id as id",
          "sau_rs_inspections.name as name"
        )
        ->orderBy('name')
        ->pluck('id', 'name');

      return $this->multiSelectFormat($inspections);
    }

    /**
     * Returns an arrangement with the types
     *
     * @return Array
     */
    public function multiselectTypes()
    {
        $types = TypeInspections::select(
            "sau_ph_type_inspections.id as id",
            "sau_ph_type_inspections.type as type")
            ->where('id', '<>', 3)
            ->orderBy('type')
            ->pluck('id', 'type');

        return $this->multiSelectFormat($types);
    }

    public function multiselectTypesItems()
    {
        $types = TypeInspectionsItems::select(
            "sau_ph_inspetions_type_items.id as id",
            "sau_ph_inspetions_type_items.type as type")
            ->orderBy('type')
            ->pluck('id', 'type');

        return $this->multiSelectFormat($types);
    }


    public function multiselectQualification()
    {
        $qualification = Qualifications::select(
            "sau_ph_qualifications_inspections.id as id",
            "sau_ph_qualifications_inspections.description as name")
            ->where('id', '<=', 4)
            ->orderBy('description')
            ->pluck('id', 'name');

        return $this->multiSelectFormat($qualification);
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new InspectionImportTemplateExcel(collect([]), $this->company), 'PlantillaImportacionInspecciones.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        InspectionImportJob::dispatch($request->file, $request->except('file'), $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function storeQualificationOption(Request $request)
    {
        $optionsSave = [];

        if ($request['options'])
            $optionsSave = $this->getDataFromMultiselect($request['options']);

        $company = Company::find($this->company);
        $company->qualificationMasiveRs()->sync($optionsSave);

        return $this->respondHttp200([
            'message' => 'Se actualizó la configuración'
        ]);
    }

    public function getQualificationOption()
    {
        $company = Company::find($this->company);
        $qualifications = [];

        foreach ($company->qualificationMasiveRs as $key => $value)
        {
            array_push($qualifications, $value->multiselect());
        }

        return $this->respondHttp200([
            'data' => $qualifications,
        ]);
    }

    public function getFiltersUsers()
    {
        $value = '';
        $message = '';
        $isSuper = $this->user->hasRole('Superadmin', $this->team);

        if (!$isSuper)
        {
            try
            {
                $configLevel = ConfigurationsCompany::company($this->company)->findByKey('filter_inspections');
            } catch (\Exception $e) {
                $configLevel = 'NO';
            }

            if ($configLevel == 'SI')
            {
                $locationLevelForm = ConfigurationsCompany::company($this->company)->findByKey('location_level_form_user_inspection_filter');

                if ($locationLevelForm == 'Regional')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');

                    if (count($regionals) > 0)
                    {
                        $value = false;
                    }
                    else
                    {
                        $value = true;
                        $message = 'Debe configurar el nivel de localización al usuario';
                    }
                        
                }
                else if ($locationLevelForm == 'Sede')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0)
                    {
                        $value = false;
                    }
                    else
                    {
                        $value = true;
                        $message = 'Debe configurar los niveles de localización al usuario';
                    }
                }
                else if ($locationLevelForm == 'Proceso')
                {
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                    $processes = User::find($this->user->id)->processes()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0)
                    {
                        $value = false;
                    }
                    else
                    {
                        $value = true;
                        $message = 'Debe configurar los niveles de localización al usuario';
                    }
                }
                else if ($locationLevelForm == 'Área')
                {                        
                    $regionals = User::find($this->user->id)->regionals()->pluck('id');
                    $headquarters = User::find($this->user->id)->headquartersFilter()->pluck('id');
                    $processes = User::find($this->user->id)->processes()->pluck('id');
                    $areas = User::find($this->user->id)->areas()->pluck('id');

                    if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0 && count($areas) > 0)
                    {
                        $value = false;
                    }
                    else
                    {
                        $value = true;
                        $message = 'Debe configurar los niveles de localización al usuario';
                    }
                }
            }
        }

        return $this->respondHttp200([
            'data' => [
                'value' => $value,
                'message' => $message
            ]
        ]);
    }
}
