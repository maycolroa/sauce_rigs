<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Users\User;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSendPdf;
use App\Models\General\Company;
use App\Traits\LocationFormTrait;
use Carbon\Carbon;
use DB;
use PDF;

class InspectionQualificationSendPdfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($token)
    {
        $errorMenssage = '';
        $data = collect([]);

        $sendPDf = InspectionSendPdf::where('token', $token)->first();

        if ($sendPDf)
        {
            $qualification = InspectionItemsQualificationAreaLocation::withoutGlobalScopes()->where('sau_ph_inspection_items_qualification_area_location.qualification_date', $sendPDf->qualification_date)->first();

            if (!$qualification)
                $errorMenssage = 'La inspeccion calificada a la cual desea acceder no existe';
        }
        else
            $errorMenssage = 'La solicitud de pdf no exite';

        
        return $this->downloadPdf($qualification->id, $sendPDf->company_id);
    }

    public function downloadPdf($id, $company_id)
    {
        $inspections = $this->getDataExportPdf($id, $company_id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.inspectionsDangerousConditionsLogout', ['inspections' => $inspections] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('inspeccion.pdf');
    }

    public function getDataExportPdf($id, $company_id)
    {
        $inspection = $this->getInformationInspection($id, $company_id);

        $company = Company::select('logo')->where('id', $company_id)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $inspection->put('logo', $logo);
        $inspection->put('keywords', $this->getKeywordQueue($company_id));

        return $inspection;
    }

    public function getInformationInspection($id, $company_id)
    {
        $qualification = InspectionItemsQualificationAreaLocation::withoutGlobalScopes()->findOrFail($id);

        $inspectionsReady = InspectionItemsQualificationAreaLocation::select(
                'sau_ph_inspection_items_qualification_area_location.*',
                DB::raw('CONCAT(sau_ph_qualifications_inspections.name, " (",  sau_ph_qualifications_inspections.description, ")") AS qualification'),
                'sau_ph_inspection_section_items.description AS item_name',
                'sau_ph_inspection_sections.name AS section_name',
                'sau_ph_inspection_sections.id AS section_id'
            )
            ->leftJoin('sau_ph_qualifications_inspections', 'sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
            ->join('sau_ph_inspection_sections', 'sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
            ->withoutGlobalScopes()
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
                $itemRow->put('qualify', $item->qualify);
                $itemRow->put('find', $item->find);
                $itemRow->put('level_risk', $item->level_risk);
                $itemRow->put('photo_1', $item->photo_1);
                $itemRow->put('old_1', $item->photo_1);
                $itemRow->put('path_1', $item->path_image('photo_1'));
                $itemRow->put('photo_2', $item->photo_2);
                $itemRow->put('old_2', $item->photo_2);
                $itemRow->put('path_2', $item->path_image('photo_2'));
                $itemRow->put('actionPlan', ActionPlan::model($item)->company($company_id)->prepareDataComponent());
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

        $firms = InspectionFirm::where('qualification_date', $qualification->qualification_date)->get();

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
                $user = User::find($firm->user_id)->withoutGlobalScopes();
                $firm_path = Storage::disk('s3')->url('administrative/firms_users/'. $user->id . '/' . $user->firm);
                $firm_add->put('image', $firm_path);
            }
            else
            {
                $firm_add->put('image', $firm->path_image('image'));
            }

            //$firm_add->put('image', $firm->path_image('image'));
            $firms_values->push($firm_add);

            if ($firms_values->count() == 2)
            {
                $firms_values_par->push($firms_values);
                $firms_values = collect([]);
            }
        }

        if ($firms_values->count() > 0)
            $firms_values_par->push($firms_values);

        $inspection_raiz = Inspection::withoutGlobalScopes()->find($qualification->item->section->inspection_id);
        
        if ($inspection_raiz->type_id == 1)
            $compliance = $this->complianceType1($id);

        
        $regional = EmployeeRegional::where('id', $qualification->employee_regional_id);
        $regional->company_scope = $company_id;
        $regional = $regional->first();

        $sede = EmployeeHeadquarter::where('id', $qualification->employee_headquarter_id);
        $sede->company_scope = $company_id;
        $sede = $sede->first();

        $proceso = EmployeeProcess::where('id', $qualification->employee_process_id);
        $proceso->company_scope = $company_id;
        $proceso = $proceso->first();

        $area = EmployeeArea::where('id', $qualification->employee_area_id);
        $area->company_scope = $company_id;
        $area = $area->first();
            
        $data = collect([]);
        $data->put('inspection', $inspection_raiz->name);
        $data->put('type', $inspection_raiz->type_id);
        $data->put('version', $inspection_raiz->version);
        $data->put('regional', $regional ? $regional->name : '');
        $data->put('headquarter', $sede ? $sede->name : '');
        $data->put('process', $proceso ? $proceso->name : '');
        $data->put('area', $area ? $area->name : '');
        $data->put('created_at', (Carbon::createFromFormat('Y-m-d H:i:s', $inspection_raiz->created_at))->format('Y-m-d H:i:s'));
        $data->put('qualification_date', $qualification->qualification_date);
        $data->put('qualifier', $qualification->qualifier ? $qualification->qualifier->name : '');
        $data->put('themes', $themes);
        $data->put('add_fields', $fields);
        $data->put('firms', $firms_values_par);

        if ($inspection_raiz->type_id == 1)
            $data->put('compliance', $compliance->p_cumple);
        else
            $data->put('compliance', null);


        return $data;
    }

    public function complianceType1($id)
    {
        $qualification = InspectionItemsQualificationAreaLocation::withoutGlobalScopes()->findOrFail($id);

        $consultas = InspectionItemsQualificationAreaLocation::select(
            DB::raw('count(sau_ph_inspection_items_qualification_area_location.qualification_id) as numero_items'),
            DB::raw('count(IF(sau_ph_qualifications_inspections.fulfillment = 1, sau_ph_qualifications_inspections.id, null)) as t_cumple'),
            DB::raw('count(IF(sau_ph_qualifications_inspections.fulfillment = 0, sau_ph_qualifications_inspections.id, null)) as t_no_cumple'),
            DB::raw('SUM(IF(sau_ph_qualifications_inspections.fulfillment = 2, sau_ph_inspections.fullfilment_parcial, 0)) as t_cumple_p')
            )
            ->join('sau_ph_inspection_section_items','sau_ph_inspection_items_qualification_area_location.item_id', 'sau_ph_inspection_section_items.id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_section_items.inspection_section_id', 'sau_ph_inspection_sections.id')
            ->join('sau_ph_inspections','sau_ph_inspection_sections.inspection_id', 'sau_ph_inspections.id')
            ->join('sau_ph_qualifications_inspections','sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id','sau_ph_inspection_items_qualification_area_location.employee_area_id')
            ->where('sau_ph_inspection_items_qualification_area_location.qualification_date', $qualification->qualification_date)
            ->withoutGlobalScopes();

        if ($qualification->employee_regional_id)
            $consultas->where('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $qualification->employee_regional_id);

        if ($qualification->employee_headquarter_id)
            $consultas->where('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $qualification->employee_headquarter_id);

        if ($qualification->employee_process_id)
            $consultas->where('sau_ph_inspection_items_qualification_area_location.employee_process_id', $qualification->employee_process_id);

        if ($qualification->employee_area_id)
            $consultas->where('sau_ph_inspection_items_qualification_area_location.employee_area_id', $qualification->employee_area_id);

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->select(
            DB::raw('ROUND( ((t_cumple + t_cumple_p) * 100) / numero_items, 1) AS p_cumple'),
            DB::raw('ROUND( (t_no_cumple * 100) / numero_items, 1) AS p_no_cumple')
        )
        ->mergeBindings($consultas->getQuery())
        ->first();

        return $consultas;
    }
}