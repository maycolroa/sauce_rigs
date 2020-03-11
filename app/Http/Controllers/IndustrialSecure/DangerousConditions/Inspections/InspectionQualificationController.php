<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections\SaveQualificationRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Carbon\Carbon;
use Validator;
use DB;
use PDF;

class InspectionQualificationController extends Controller
{
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
                'sau_ph_inspection_items_qualification_area_location.*',
                'sau_employees_headquarters.name AS headquarter',
                'sau_employees_areas.name AS area',
                'sau_users.name AS qualificator'
            )
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
            ->join('sau_users', 'sau_users.id', 'sau_ph_inspection_items_qualification_area_location.qualifier_id')
            ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
            ->where('sau_ph_inspection_sections.inspection_id', $request->inspectionId);

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $qualifications->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
            $qualifications->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
            $qualifications->inQualifiers($this->getValuesForMultiselect($filters["qualifiers"]), $filters['filtersType']['qualifiers']);

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
            ->where('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $qualification->employee_headquarter_id)
            ->where('sau_ph_inspection_items_qualification_area_location.employee_area_id', $qualification->employee_area_id)
            ->where('sau_ph_inspection_items_qualification_area_location.qualification_date', $qualification->qualification_date)
            ->get();

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
                $itemRow->put('path_1', Storage::disk('public')->url('industrialSecure/dangerousConditions/inspections/images/'. $item->photo_1));
                $itemRow->put('photo_2', $item->photo_2);
                $itemRow->put('old_2', $item->photo_2);
                $itemRow->put('path_2', Storage::disk('public')->url('industrialSecure/dangerousConditions/inspections/images/'. $item->photo_2));
                $itemRow->put('actionPlan', ActionPlan::model($item)->prepareDataComponent());
                $items->push($itemRow);
            }
            
            $theme->put('items', $items);
            $themes->push($theme);
        }

        $data = collect([]);
        $data->put('inspection', $qualification->item->section->inspection->name);
        $data->put('headquarter', $qualification->headquarter ? $qualification->headquarter->name : '');
        $data->put('area', $qualification->area ? $qualification->area->name : '');
        $data->put('created_at', (Carbon::createFromFormat('Y-m-d H:i:s', $qualification->item->section->inspection->created_at))->format('Y-m-d H:i:s'));
        $data->put('qualification_date', $qualification->qualification_date);
        $data->put('qualifier', $qualification->qualifier ? $qualification->qualifier->name : '');
        $data->put('themes', $themes);

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

            ActionPlan::
                    user($this->user)
                ->module('dangerousConditions')
                ->url(url('/administrative/actionplans'))
                ->model($qualification)
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
                Storage::disk('public')->delete('industrialSecure/dangerousConditions/inspections/images/'. $qualification->$picture);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs('industrialSecure/dangerousConditions/inspections/images/', $nameFile, 'public');
                $qualification->$picture = $nameFile;
                $data['image'] = $nameFile;
                $data['old'] = $nameFile;
                $data['path'] = Storage::disk('public')->url('industrialSecure/dangerousConditions/inspections/images/'. $nameFile);
            }
            else
            {
                Storage::disk('public')->delete('industrialSecure/dangerousConditions/inspections/images/'. $qualification->$picture);
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

        return Storage::disk('public')->download('industrialSecure/dangerousConditions/inspections/images/'. $qualification->$column);
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

        return $inspection;
    }
}