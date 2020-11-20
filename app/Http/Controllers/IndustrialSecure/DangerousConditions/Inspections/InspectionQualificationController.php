<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections\SaveQualificationRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Jobs\IndustrialSecure\DangerousConditions\Inspections\InspectionQualifyExportJob;
use App\Models\General\Company;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use Validator;
use DB;
use PDF;

class InspectionQualificationController extends Controller
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
        $this->middleware("permission:ph_qualification_inspection_d, {$this->team}", ['only' => 'destroy']);
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
        $qualifications = InspectionItemsQualificationAreaLocation::distinct()->select(
                DB::raw('MAX(sau_ph_inspection_items_qualification_area_location.id) AS id'),
                'sau_ph_inspection_items_qualification_area_location.qualification_date',
                'sau_employees_regionals.name AS regional',
                'sau_employees_headquarters.name AS headquarter',
                'sau_employees_processes.name AS process',
                'sau_employees_areas.name AS area',
                'sau_users.name AS qualificator'
            )
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
            ->join('sau_users', 'sau_users.id', 'sau_ph_inspection_items_qualification_area_location.qualifier_id')
            ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
            ->where('sau_ph_inspection_sections.inspection_id', $request->inspectionId)
            ->groupBy('sau_ph_inspection_items_qualification_area_location.qualification_date', 'regional', 'headquarter', 'process', 'area', 'qualificator');

        $url = "/industrialsecure/dangerousconditions/inspections/qualification/".$request->get('modelId');

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
        try
        {
            return $this->respondHttp200([
                'data' => $this->getInformationInspection($id),
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getInformationInspection($id)
    {
        $qualification = InspectionItemsQualificationAreaLocation::findOrFail($id);

        $inspectionsReady = InspectionItemsQualificationAreaLocation::select(
                'sau_ph_inspection_items_qualification_area_location.*',
                DB::raw('CONCAT(sau_ct_qualifications.name, " (",  sau_ct_qualifications.description, ")") AS qualification'),
                'sau_ph_inspection_section_items.description AS item_name',
                'sau_ph_inspection_sections.name AS section_name',
                'sau_ph_inspection_sections.id AS section_id'
            )
            ->leftJoin('sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
            ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
            ->where('sau_ph_inspection_items_qualification_area_location.qualification_date', $qualification->qualification_date);

        if ($qualification->employee_regional_id)
            $inspectionsReady->where('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $qualification->employee_regional_id);

        if ($qualification->employee_headquarter_id)
            $inspectionsReady->where('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $qualification->employee_headquarter_id);

        if ($qualification->employee_process_id)
            $inspectionsReady->where('sau_ph_inspection_items_qualification_area_location.employee_process_id', $qualification->employee_process_id);

        if ($qualification->employee_area_id)
            $inspectionsReady->where('sau_ph_inspection_items_qualification_area_location.employee_area_id', $qualification->employee_area_id);
            
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
                $itemRow->put('find', $item->find);
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

        $add_fields = AdditionalFieldsValues::where('qualification_date', $qualification->qualification_date)->get();

        $fields = collect([]);

        foreach ($add_fields as $fieldKey => $field)
        {
            $field_add = collect([]);

            $add = AdditionalFields::find($field['field_id']);

            $field_add->put('key', Carbon::now()->timestamp + rand(1,10000));
            $field_add->put('name', $add->name);
            $field_add->put('value', $field['value']);
            $fields->push($field_add);
        }

        $data = collect([]);
        $data->put('inspection', $qualification->item->section->inspection->name);
        $data->put('regional', $qualification->regional ? $qualification->regional->name : '');
        $data->put('headquarter', $qualification->headquarter ? $qualification->headquarter->name : '');
        $data->put('process', $qualification->process ? $qualification->process->name : '');
        $data->put('area', $qualification->area ? $qualification->area->name : '');
        $data->put('created_at', (Carbon::createFromFormat('Y-m-d H:i:s', $qualification->item->section->inspection->created_at))->format('Y-m-d H:i:s'));
        $data->put('qualification_date', $qualification->qualification_date);
        $data->put('qualifier', $qualification->qualifier ? $qualification->qualifier->name : '');
        $data->put('themes', $themes);
        $data->put('add_fields', $fields);

        return $data;
    }

    public function saveQualification(SaveQualificationRequest $request)
    {
        try
        {
            DB::beginTransaction();

            $data = $request->except('themes');
            $data['id_item_qualification'] = (int) $data['id_item_qualification'];

            $qualification = InspectionItemsQualificationAreaLocation::findOrFail($request->id_item_qualification);

            $inspection = $qualification->item->section->inspection;
            $theme = $qualification->item->section;
            $item = $qualification->item;

            $details = 'Inspección: ' . $inspection->name . ' - ' . $theme->name . ' - ' . $item->description;

            $regionals = $inspection->regionals ? $inspection->regionals->implode('name', ', ') : null;
            $headquarters =  $inspection->headquarters ? $inspection->headquarters->implode('name', ', ') : null;
            $processes = $inspection->processes ? $inspection->processes->implode('name', ', ') : null;
            $areas = $inspection->areas ? $inspection->areas->implode('name', ', ') : null;

            ActionPlan::
                    user($this->user)
                ->module('dangerousConditions')
                ->url(url('/administrative/actionplans'))
                ->model($qualification)
                ->regional($regionals)
                ->headquarter($headquarters)
                ->area($areas)
                ->process($processes)
                ->details($details)
                ->activities($request->actionPlan)
                ->save();

            $data['actionPlan'] = ActionPlan::getActivities();

            ActionPlan::sendMail();

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

        $qualification = InspectionItemsQualificationAreaLocation::findOrFail($request->id);
        $data = $request->all();
        $picture = $request->column;

        if ($request->image != $qualification->$picture)
        {
            if ($request->image)
            {
                $file = $request->image;
                $qualification->img_delete($picture);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs($qualification->path_base(), $nameFile, 's3_DConditions');
                $qualification->$picture = $nameFile;
                $data['image'] = $nameFile;
                $data['old'] = $nameFile;
                $data['path'] = Storage::disk('s3_DConditions')->url($qualification->path_base(). $nameFile);
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

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    public function downloadImage($id, $column)
    {
        $qualification = InspectionItemsQualificationAreaLocation::findOrFail($id);
        return $qualification->donwload_img($column);
    }

    public function downloadPdf($id)
    {
        $inspections = $this->getDataExportPdf($id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.inspectionsDangerousConditions', ['inspections' => $inspections] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('inspeccion.pdf');
    }

    public function getDataExportPdf($id)
    {
        $inspection = $this->getInformationInspection($id);

        $company = Company::select('logo')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

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
        //DB::beginTransaction();

        $qualification = InspectionItemsQualificationAreaLocation::findOrFail($qualification);

        $items = InspectionItemsQualificationAreaLocation::where('qualification_date', $qualification->qualification_date)->get();

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
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la inspección realizada'
        ]);
    }
}