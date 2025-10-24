<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\RoadSafety\Inspections\Inspection;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSection;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionQualified;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionItemsQualificationLocation;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Users\User;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections\SaveQualificationRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionQualifyExportJob;
use App\Models\General\Company;
use App\Traits\Filtertrait;
use App\Traits\LocationFormTrait;
use Carbon\Carbon;
use Validator;
use DB;
use PDF;

class InspectionQualificationController extends Controller
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
        //$this->middleware("permission:ph_inspections_r, {$this->team}");
        //$this->middleware('permission:ph_inspections_u', ['only' => 'update']);        
        //$this->middleware("permission:ph_qualification_inspection_d, {$this->team}", ['only' => 'destroy']);
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
        $qualifications = InspectionQualified::select(
                'sau_rs_inspections_qualified.id',
                'sau_rs_inspections_qualified.qualification_date',
                'sau_employees_regionals.name AS regional',
                'sau_employees_headquarters.name AS headquarter',
                'sau_employees_processes.name AS process',
                'sau_employees_areas.name AS area',
                'sau_users.name AS qualificator',
                'sau_rs_vehicles.plate AS plate'
            )
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rs_inspections_qualified.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rs_inspections_qualified.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rs_inspections_qualified.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rs_inspections_qualified.employee_area_id')
            ->join('sau_users', 'sau_users.id', 'sau_rs_inspections_qualified.qualifier_id')
            ->join('sau_rs_vehicles', 'sau_rs_vehicles.id', 'sau_rs_inspections_qualified.vehicle_id')
            ->where('sau_rs_inspections_qualified.inspection_id', $request->inspectionId)
            ->groupBy('sau_rs_inspections_qualified.id', 'regional', 'headquarter', 'process', 'area', 'qualificator')
            ->orderBy('sau_rs_inspections_qualified.id', 'DESC');

        $url = "/industrialsecure/roadsafety/inspections/qualification/".$request->get('modelId');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $qualifications->inQualifiers($this->getValuesForMultiselect($filters["qualifiers"]), $filters['filtersType']['qualifiers']);

            if (isset($filters["regionals"]))
                $qualifications->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $qualifications->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $qualifications->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $qualifications->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $qualifications->betweenDate($dates);
        }

        return Vuetable::of($qualifications)
                    ->make();
    }

    public function show($id)
    {
        \Log::info($id);
        try
        {
            return $this->respondHttp200([
                'data' => $this->getInformationInspection($id),
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function getInformationInspection($id)
    {
        $qualification = InspectionQualified::withoutGlobalScopes()->findOrFail($id);

        $inspectionsReady = InspectionItemsQualificationLocation::select(
                'sau_rs_inspection_items_qualifications_locations.*',
                DB::raw('CONCAT(sau_ph_qualifications_inspections.name, " (",  sau_ph_qualifications_inspections.description, ")") AS qualification'),
                'sau_rs_inspection_section_items.description AS item_name',
                'sau_rs_inspection_sections.name AS section_name',
                'sau_rs_inspection_sections.id AS section_id'
            )
            ->leftJoin('sau_ph_qualifications_inspections', 'sau_ph_qualifications_inspections.id', 'sau_rs_inspection_items_qualifications_locations.qualification_id')
            ->join('sau_rs_inspection_section_items', 'sau_rs_inspection_section_items.id', 'sau_rs_inspection_items_qualifications_locations.item_id')
            ->join('sau_rs_inspection_sections', 'sau_rs_inspection_sections.id', 'sau_rs_inspection_section_items.inspection_section_id')
            ->withoutGlobalScopes()
            ->where('sau_rs_inspection_items_qualifications_locations.inspection_qualification_id', $qualification->id);
            
        $inspectionsReady = $inspectionsReady->get();
        $inspectionsReady = $inspectionsReady->groupBy('section_id');

        $themes = collect([]);

        foreach ($inspectionsReady as $themeKey => $itemsData)
        {
            $theme = collect([]);
            $theme->put('key', Carbon::now()->timestamp + rand(1,10000));
            $theme->put('name', $itemsData[0]->section_name);

            $items = collect([]);
            
            foreach ($itemsData as $itemKey => $item)
            {
                $itemRow = collect([]);
                $itemRow->put('key', Carbon::now()->timestamp + rand(1,10000));
                $itemRow->put('id_item_qualification', $item->id);
                $itemRow->put('description', $item->item_name);
                $itemRow->put('qualification', $item->qualification);
                $itemRow->put('qualify', $item->qualify);
                $itemRow->put('find', $item->find);
                $itemRow->put('level_risk', $item->level_risk);
                $itemRow->put('photo_1', $item->photo_1);
                $itemRow->put('old_1', $item->photo_1);
                $itemRow->put('path_1', $item->path_image('photo_1'));
                $itemRow->put('photo_2', $item->photo_2);
                $itemRow->put('old_2', $item->photo_2);
                $itemRow->put('path_2', $item->path_image('photo_2'));
                $itemRow->put('actionPlan', ActionPlan::model($item)->company($this->company)->prepareDataComponent());
                $items->push($itemRow);
            }
            
            $theme->put('items', $items);
            $themes->push($theme);
        }

        $firms = InspectionFirm::where('inspection_qualification_id', $qualification->id)->get();

        $firms_values_par = collect([]);
        $firms_values = collect([]);

        foreach ($firms as $firmKey => $firm)
        {
            $firm_add = collect([]);
            $firm_add->put('key', Carbon::now()->timestamp + rand(1,10000));
            $firm_add->put('name', $firm->name);
            $firm_add->put('identification', $firm->identification);

            if (!$firm->image && $firm->state == 'Ingresada')
            {
                $user = User::find($firm->user_id);
                $firm_path = Storage::disk('s3')->url('administrative/firms_users/'. $user->id . '/' . $user->firm);
                $firm_add->put('image', $firm_path);
            }
            else
            {
                $firm_add->put('image', $firm->path_image('image'));
            }

            $firms_values->push($firm_add);

            if ($firms_values->count() == 2)
            {
                $firms_values_par->push($firms_values);
                $firms_values = collect([]);
            }
        }

        if ($firms_values->count() > 0)
            $firms_values_par->push($firms_values);
            
        $data = collect([]);
        $data->put('inspection', $qualification->inspection->name);
        $data->put('type', $qualification->inspection->type_id);
        $data->put('version', $qualification->inspection->version);
        $data->put('regional', $qualification->regional ? $qualification->regional->name : '');
        $data->put('headquarter', $qualification->headquarter ? $qualification->headquarter->name : '');
        $data->put('process', $qualification->process ? $qualification->process->name : '');
        $data->put('area', $qualification->area ? $qualification->area->name : '');
        $data->put('created_at', (Carbon::createFromFormat('Y-m-d H:i:s', $qualification->inspection->created_at))->format('Y-m-d H:i:s'));
        $data->put('qualification_date', $qualification->qualification_date);
        $data->put('qualifier', $qualification->qualifier ? $qualification->qualifier->name : '');
        $data->put('themes', $themes);
        $data->put('firms', $firms_values_par);

        $data->put('compliance', null);

        return $data;
    }

    public function saveQualification(SaveQualificationRequest $request)
    {
        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();

        try
        {
            DB::beginTransaction();

            $data = $request->except('themes');
            $data['id_item_qualification'] = (int) $data['id_item_qualification'];

            $qualification = InspectionItemsQualificationLocation::findOrFail($request->id_item_qualification);

            $inspection = $qualification->item->section->inspection;
            $theme = $qualification->item->section;
            $item = $qualification->item;

            $details = 'Inspección: ' . $inspection->name . ' - ' . $theme->name . ' - ' . $item->description;

            if ($confLocation['regional'] == 'SI')
                $detail_procedence = 'Seguridad Vial - Inspecciones Planeadas. Placa de vehiculo: '. $qualification->qualified->vehicle->plate. ' - ' . $details . '- ' . $keywords['regional']. ': ' .  $qualification->qualified->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $qualification->qualified->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $qualification->qualified->process->name;
            if ($confLocation['area'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $qualification->qualified->area->name;

            ActionPlan::
                    user($this->user)
                ->module('roadSafety')
                ->url(url('/administrative/actionplans'))
                ->model($qualification)
                ->regional($qualification->qualified->regional->name)
                ->headquarter($qualification->qualified->headquarter ? $qualification->qualified->headquarter->name : '')
                ->area($qualification->qualified->area ? $qualification->qualified->area->name : '')
                ->process($qualification->qualified->process ? $qualification->qualified->process->name : '')
                ->details($details)
                ->detailProcedence($detail_procedence)
                ->activities($request->actionPlan)
                ->save();

            $data['actionPlan'] = ActionPlan::getActivities();

            ActionPlan::sendMail();

            $this->saveLogActivitySystem('Seguridad vial - Inspecciones planeadas', 'Se edito la calificacion realizada en '.$detail_procedence);

            DB::commit();

            return $this->respondHttp200([
                'data' => $data
            ]);

        } catch (Exception $e){
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function saveImage(Request $request)
    {
        \Log::info($request);
        Validator::make($request->all(), [
            "image" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();


        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();

        $qualification = InspectionItemsQualificationLocation::findOrFail($request->id);
        $data = $request->all();
        $picture = $request->column;

        if ($request->image != $qualification->$picture)
        {
            if ($request->image)
            {
                $file = $request->image;
                $qualification->img_delete($picture);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->getClientOriginalExtension();
                $file->storeAs($qualification->path_base(), $nameFile, 's3');
                $qualification->$picture = $nameFile;
                $data['image'] = $nameFile;
                $data['old'] = $nameFile;
                $data['path'] = Storage::disk('s3')->url($qualification->path_base(). $nameFile);
            }
            else
            {
                $qualification->img_delete($picture);
                $qualification->$picture = NULL;
                $data['image'] = "";
                $data['old'] = NULL;
                $data['path'] = NULL;
            }
        }

        if (!$qualification->update())
            return $this->respondHttp500();

        $inspection = $qualification->item->section->inspection;
        $theme = $qualification->item->section;
        $item = $qualification->item;

        $details = 'Inspección: ' . $inspection->name . ' - ' . $theme->name . ' - ' . $item->description;

        if ($confLocation['regional'] == 'SI')
            $detail_procedence = 'Seguridad Vial - Inspecciones Planeadas. Placa de vehiculo: '. $qualification->qualified->vehicle->plate. ' - ' . $details . '- ' . $keywords['regional']. ': ' .  $qualification->qualified->regional->name;
        if ($confLocation['headquarter'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $qualification->qualified->headquarter->name;
        if ($confLocation['process'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $qualification->qualified->process->name;
        if ($confLocation['area'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $qualification->qualified->area->name;

        $this->saveLogActivitySystem('Seguridad Vial - Inspecciones planeadas', 'Se cargaron imagenes a la calificacion realizada en '.$detail_procedence);

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    public function downloadImage($id, $column)
    {
        $qualification = InspectionItemsQualificationLocation::findOrFail($id);
        return $qualification->donwload_img($column);
    }

    public function downloadPdf($id)
    {
        $inspections = $this->getDataExportPdf($id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.inspectionsRoadSafety', ['inspections' => $inspections] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('inspeccion.pdf');
    }

    public function getDataExportPdf($id)
    {
        $inspection = $this->getInformationInspection($id);

        $company = Company::select('logo')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;
        $logo = $logo ? Storage::disk('s3')->url('administrative/logos/'.$logo) : null;

        $inspection->put('logo', $logo);

        return $inspection;
    }

    public function exportQualify(Request $request)
    {
        try
        {
            $dates = [];
            $dates_request = explode('/', $request->dateRange);

            $date1 = Carbon::createFromFormat('D M d Y', $dates_request[0]);
            $date2 = Carbon::createFromFormat('D M d Y', $dates_request[1]);

            $regionals = $request->regionals ? $this->getValuesForMultiselect($request->regionals) : [];
            $headquarters = $request->headquarters ? $this->getValuesForMultiselect($request->headquarters) : [];
            $processes = $request->processes ? $this->getValuesForMultiselect($request->processes) : [];
            $areas = $request->areas ? $this->getValuesForMultiselect($request->areas) : [];
            $qualifiers = $this->getValuesForMultiselect($request->qualifiers);
            $filtersType = $request->filtersType;

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $date1->format('Y-m-d 00:00:00'));
                array_push($dates, $date2->format('Y-m-d 23:59:59'));
            }

            if($date1->diffInMonths($date2) > 6)
            {
                return $this->respondWithError('El rango de fecha ingresado no puede ser mayor a 6 meses');
            }

            $filters = [
                'qualifiers' => $qualifiers,
                'regionals' => $regionals,
                'headquarters' => $headquarters,
                'processes' => $processes,
                'areas' => $areas,
                'dates' => $dates,
                'filtersType' => $filtersType
            ];

            InspectionQualifyExportJob::dispatch($this->user, $this->company, $filters, $request->id);
          
            return $this->respondHttp200();

        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function destroy($qualification)
    {
        DB::beginTransaction();

        $qualification = InspectionQualified::findOrFail($qualification);

        $items = InspectionItemsQualificationLocation::where('inspection_qualification_id', $qualification->id)->get();

        $firms = InspectionFirm::where('inspection_qualification_id', $qualification->id)->get();

        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();
        $inspection = $qualification->inspection;
        $description_delete = '';

        if ($confLocation['regional'] == 'SI')
            $description_delete = $keywords['regional']. ': ' .  $qualification->regional->name;
        if ($confLocation['headquarter'] == 'SI')
            $description_delete = $description_delete . ' - ' .$keywords['headquarter']. ': ' .  $qualification->headquarter->name;
        if ($confLocation['process'] == 'SI')
            $description_delete = $description_delete . ' - ' .$keywords['process']. ': ' .  $qualification->process->name;
        if ($confLocation['area'] == 'SI')
            $description_delete = $description_delete . ' - ' .$keywords['area']. ': ' .  $qualification->area->name;

        $this->saveLogDelete('Seguridad Vial - Inspecciones planeadas', 'Se elimino la inspección '. $inspection->name .' realizada en '.$description_delete . ', al vehiculo con placa: '. $qualification->vehicle->plate);

        try
        { 
            foreach ($items as $item)
            {  
                ActionPlan::model($item)->modelDeleteAll();

                $item->img_delete('photo_1');
                $item->img_delete('photo_2');

                if(!$item->delete())
                {
                    return $this->respondHttp500();
                }
            }

            foreach ($firms as $firm)
            {
                if(!$firm->delete())
                {
                    return $this->respondHttp500();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la inspección realizada'
        ]);
    }
}