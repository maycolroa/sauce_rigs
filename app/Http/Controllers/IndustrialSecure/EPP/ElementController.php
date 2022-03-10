<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\TagsMark;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\ElementBalanceInicialLog;
use App\Models\IndustrialSecure\Epp\TagsApplicableStandard;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Http\Requests\IndustrialSecure\Epp\ElementRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Exports\IndustrialSecure\Epp\ElementImportTemplateExcel;
use App\Exports\IndustrialSecure\Epp\ElementNotIdentExcel;
use App\Exports\IndustrialSecure\Epp\ElementIdentExcel;
use App\Jobs\IndustrialSecure\Epp\ElementImportJob;
use App\Jobs\IndustrialSecure\Epp\ElementBalanceInitialImportJob;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Filtertrait;

class ElementController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:elements_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:elements_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:elements_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:elements_d, {$this->team}", ['only' => 'destroy']);
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
        $elements = Element::selectRaw(
            "sau_epp_elements.*,
            case when sau_epp_elements.state is true then 'SI' else 'NO' end as state,
            case when sau_epp_elements.identify_each_element is true then 'SI' else 'NO' end as identy,
            case when sau_epp_elements.reusable is true then 'SI' else 'NO' end as reusable"
        );

        return Vuetable::of($elements)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\ElementRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementRequest $request)
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

        $types = $this->tagsPrepare($request->get('type'));
        $this->tagsSave($types, TagsType::class);


        $mark = $this->tagsPrepare($request->get('mark'));
        $this->tagsSave($mark, TagsMark::class);

        $standar_apply = $this->tagsPrepare($request->get('applicable_standard'));
        $this->tagsSaveSystemCompany($standar_apply, TagsApplicableStandard::class);

        $element = new Element();
        $element->name = $request->name;
        $element->code = $request->code;
        $element->description = $request->description;
        $element->observations = $request->observations;
        $element->operating_instructions = $request->operating_instructions;
        $element->applicable_standard = $standar_apply->implode(',');
        $element->state = $request->state == "Activo" ? true : false;
        $element->reusable = $request->reusable == "SI" ? true : false;
        $element->identify_each_element = $request->identify_each_element == "SI" ? true : false;
        $element->expiration_date = $request->expiration_date == "SI" ? true : false;
        $element->days_expired = $request->expiration_date == "SI" ? $request->days_expired : NULL;
        $element->company_id = $this->company;
        $element->type = $types->implode(',');
        $element->mark = $mark->implode(',');
        
        if(!$element->save())
            return $this->respondHttp500();

        if ($request->image)
        {
            $file_tmp = $request->image;
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->extension();
            $file_tmp->storeAs($element->path_client(false), $nameFile, 's3');
            $element->image = $nameFile;

            if(!$element->update())
                return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el elemento'
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
            $element = Element::findOrFail($id);

            $element->state = $element->state ? 'Activo' : 'Inactivo';
            $element->reusable = $element->reusable ? 'SI' : 'NO';
            $element->identify_each_element = $element->identify_each_element ? 'SI' : 'NO';
            $element->expiration_date = $element->expiration_date ? 'SI' : 'NO';
            if ($element->image)
                $element->path = $element->path_image();
            else
                $element->path = NULL;

            return $this->respondHttp200([
                'data' => $element,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\elements\ElementRequest  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(ElementRequest $request, Element $element)
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

        $types = $this->tagsPrepare($request->get('type'));
        $this->tagsSave($types, TagsType::class);


        $mark = $this->tagsPrepare($request->get('mark'));
        $this->tagsSave($mark, TagsMark::class);


        $standar_apply = $this->tagsPrepare($request->get('applicable_standard'));
        $this->tagsSaveSystemCompany($standar_apply, TagsApplicableStandard::class);

        $element->name = $request->name;
        $element->code = $request->code;
        $element->description = $request->description;
        $element->observations = $request->observations;
        $element->operating_instructions = $request->operating_instructions;
        $element->applicable_standard = $standar_apply->implode(',');
        $element->state = $request->state == "Activo" ? true : false;
        $element->reusable = $request->reusable == "SI" ? true : false;
        $element->identify_each_element = $request->identify_each_element == "SI" ? true : false;
        $element->expiration_date = $request->expiration_date == "SI" ? true : false;
        $element->days_expired = $request->expiration_date == "SI" ? $request->days_expired : NULL;
        $element->type = $types->implode(',');
        $element->mark = $mark->implode(',');
        
        if(!$element->update()){
          return $this->respondHttp500();
        }

        if ($request->image != $element->image)
        {
            $file_tmp = $request->image;
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->extension();
            $file_tmp->storeAs($element->path_client(false), $nameFile, 's3');
            $element->image = $nameFile;

            if(!$element->update())
                return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el elemento'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(Element $element)
    {
        Storage::disk('s3')->delete($element->path_client(false)."/".$element->image);

        if(!$element->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el peligro'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $elements = Element::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                });
                //->take(30)->pluck('id', 'name');

                if ($request->has('type') && $request->get('type') != '')
                {
                    $type = $request->get('type');
                    
                    if ($type == 'Identificable')
                        $elements->where('identify_each_element', true);
                    else
                        $elements->where('identify_each_element', false);
                }

                $elements = $elements->orderBy('name')->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($elements)
            ]);
        }
        else
        {
            $elements = Element::selectRaw("
                sau_epp_elements.id as id,
                sau_epp_elements.name as name
            ")            
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($elements);
        }
    }

    public function multiselectTypes(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsType::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectMarks(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsMark::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        } 
        else
        {
            $tags = TagsMark::selectRaw("
                sau_epp_tags_marks.id as id,
                sau_epp_tags_marks.name as name
            ")            
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectApplicableStandard(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsApplicableStandard::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where('system', true)
                ->orWhere('company_id', $this->company)
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function downloadImage(Element $element)
    {
        $name = $element->image;

        return Storage::disk('s3')->download($element->path_donwload(), $name);                                               
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new ElementImportTemplateExcel(collect([]), $this->company), 'PlantillaImportacionElementos.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        ElementImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function elementNotIdentImport()
    {
      return Excel::download(new ElementNotIdentExcel($this->company), 'PlantillaImportacionSaldos.xlsx');
    }

    public function elementIdentImport()
    {
      return Excel::download(new ElementIdentExcel($this->company), 'PlantillaImportacionSaldos.xlsx');
    }

    public function importBalanceInicial(Request $request)
    {
        try
        {
            ElementBalanceInitialImportJob::dispatch($request->type_element, $request->file, $this->company, $this->user);
        
            return $this->respondHttp200();

        } catch(Exception $e)
        {
            return $this->respondHttp500();
        }
    }

    public function reportBalance(Request $request)
    {
        $report = ElementBalanceLocation::selectRaw("
            sau_epp_elements.name as element,
            sau_epp_elements.mark as mark,
            sau_epp_locations.name as location,
            count(sau_epp_elements_balance_specific.id) as quantity,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS quantity_available,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Asignado', 1, 0)) AS quantity_allocated,
            SUM(IF(sau_epp_elements_balance_specific.state = 'No Disponible', 1, 0)) AS quantity_transfer
        ")
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->groupBy('element','location', 'mark');

        $url = "/industrialsecure/epps/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["marks"]) && COUNT($filters["marks"]) > 0)
            {
                $marks = $this->getValuesForMultiselect($filters["marks"]);

                if ($filters['filtersType']['marks'] == 'IN')
                    $report->whereIn('sau_epp_elements.mark', $marks);

                else if ($filters['filtersType']['marks'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.mark', $marks);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);
        }

        
        return Vuetable::of($report)
                    ->make();
    }

    public function reportEmployee(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_employees.name as employee,
            sau_epp_elements.id as id,
            sau_epp_elements.name as element,
            sau_epp_locations.name as location,
            count(sau_epp_elements_balance_specific.id) as cantidad
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_specific.location_id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        ->whereNull('sau_epp_transactions_employees.state')
        ->groupBy('sau_employees.name', 'sau_epp_elements.id', 'sau_epp_elements.name','sau_epp_locations.name');

        $url = "/industrialsecure/epps/report";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["marks"]) && COUNT($filters["marks"]) > 0)
            {
                $marks = $this->getValuesForMultiselect($filters["marks"]);

                if ($filters['filtersType']['marks'] == 'IN')
                    $report->whereIn('sau_epp_elements.mark', $marks);

                else if ($filters['filtersType']['marks'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.mark', $marks);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);
        }

        
        return Vuetable::of($report)
                    ->make();
    }
}
