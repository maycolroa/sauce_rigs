<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections\SaveQualificationRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Traits\Filtertrait;
use App\Traits\LocationFormTrait;
use Carbon\Carbon;
use Validator;
use DB;
use PDF;

class InspectionRequestFirmController extends Controller
{
    use Filtertrait, LocationFormTrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:ph_inspections_r, {$this->team}");
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
                'sau_users.name AS qualificator',
                'u.name as user_firm'
            )
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_items_qualification_area_location.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_items_qualification_area_location.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_items_qualification_area_location.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_items_qualification_area_location.employee_area_id')
            ->join('sau_users', 'sau_users.id', 'sau_ph_inspection_items_qualification_area_location.qualifier_id')
            ->join('sau_ph_inspection_section_items', 'sau_ph_inspection_section_items.id', 'sau_ph_inspection_items_qualification_area_location.item_id')
            ->join('sau_ph_inspection_sections','sau_ph_inspection_sections.id', 'sau_ph_inspection_section_items.inspection_section_id')
            ->join('sau_ph_qualification_inspection_firm', function ($join) 
            {
                $join->on("sau_ph_qualification_inspection_firm.qualification_date", "=", "sau_ph_inspection_items_qualification_area_location.qualification_date");
                $join->on("sau_ph_qualification_inspection_firm.company_id", "=", DB::raw("{$this->company}"));
                $join->on("sau_ph_qualification_inspection_firm.state", "=", DB::raw("'Pendiente'"));
            })
            ->join( 'sau_users AS u', 'u.id', 'sau_ph_qualification_inspection_firm.user_id');

            if (!$this->user->hasRole('Administración Inspecciones', $this->team) && !$this->user->hasRole('Superadmin', $this->team))
            {
                $qualifications->where('sau_ph_qualification_inspection_firm.user_id', DB::raw("{$this->user->id}"));
            }

            $qualifications->groupBy('sau_ph_inspection_items_qualification_area_location.qualification_date', 'regional', 'headquarter', 'process', 'area', 'qualificator', 'sau_ph_qualification_inspection_firm.id', 'user_firm');

        $url = "/industrialsecure/dangerousconditions/inspections/request/firms";

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

            if (isset($filters["user_firm"]))
                $qualifications->inUserFirm($this->getValuesForMultiselect($filters["user_firm"]), $filters['filtersType']['user_firm']);

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

    public function showInspection($id)
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
                DB::raw('CONCAT(sau_ph_qualifications_inspections.name, " (",  sau_ph_qualifications_inspections.description, ")") AS qualification'),
                'sau_ph_inspection_section_items.description AS item_name',
                'sau_ph_inspection_sections.name AS section_name',
                'sau_ph_inspection_sections.id AS section_id'
            )
            ->leftJoin('sau_ph_qualifications_inspections', 'sau_ph_qualifications_inspections.id', 'sau_ph_inspection_items_qualification_area_location.qualification_id')
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

        
        if ($qualification->item->section->inspection->type_id == 1)
            $compliance = $this->complianceType1($id);
            
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
        $data->put('firms', $firms_values_par);

        if ($qualification->item->section->inspection->type_id == 1)
            $data->put('compliance', $compliance->p_cumple);
        else
            $data->put('compliance', null);


        return $data;
    }

    public function complianceType1($id)
    {
        $qualification = InspectionItemsQualificationAreaLocation::findOrFail($id);

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
            ->where('sau_ph_inspection_items_qualification_area_location.qualification_date', $qualification->qualification_date);

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

    public function saveFirm(Request $request)
    {
        $user = User::find($this->user->id);

        if ($user->firm)
        {
            $qualification = InspectionItemsQualificationAreaLocation::where('qualification_date', $request->qualification_date)->first();

            $firm = InspectionFirm::where('qualification_date', $qualification->qualification_date)->where('state', 'Pendiente')->where('user_id', $this->user->id)->first();

            if ($firm)
            {
                $firm->state = "Ingresada";
                $firm->update();
            }
            else
            {
                return $this->respondWithError('No puede firmar la inspeccion ya que no es el usuario firmante.');
            }

            return $this->respondHttp200([
                'message' => 'Inspeccion firmada'
            ]);
        }
        else
        {
            return $this->respondWithError('Debe ingresar una firma al sistema.');
        }

        

        
    }
}