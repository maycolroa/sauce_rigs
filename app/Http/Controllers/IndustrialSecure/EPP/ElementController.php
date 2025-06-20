<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\TagsMark;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\Location;
use App\Models\IndustrialSecure\Epp\ElementBalanceInicialLog;
use App\Models\IndustrialSecure\Epp\TagsApplicableStandard;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementStockMinimun;
use App\Http\Requests\IndustrialSecure\Epp\ElementRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Exports\IndustrialSecure\Epp\ElementImportTemplateExcel;
use App\Exports\IndustrialSecure\Epp\ElementNotIdentExcel;
use App\Exports\IndustrialSecure\Epp\ElementIdentExcel;
use App\Jobs\IndustrialSecure\Epp\ElementImportJob;
use App\Jobs\IndustrialSecure\Epp\ElementImportStockMinimunJob;
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
        )
        ->orderBy('id', 'DESC');

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
            ],
            "data_sheet" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'application/pdf' && 
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')

                        $fail('Ficha Técnica debe ser pdf, Excel .xlsx o Word .docx');
                },
            ],
            "user_manual" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'application/pdf' && 
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')

                        $fail('Manual de uso debe ser pdf, Excel .xlsx o Word .docx');
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
        $element->serial = $request->serial;
        $element->reference = $request->reference;
        $element->size = $request->size;
        $element->class_element = $request->class_element;
        $element->observations = $request->observations;
        $element->operating_instructions = $request->operating_instructions;
        $element->applicable_standard = $standar_apply->implode(',');
        $element->state = $request->state == "Activo" ? true : false;
        $element->reusable = $request->reusable == "SI" ? true : false;
        $element->identify_each_element = false;
        $element->expiration_date = $request->expiration_date == "SI" ? true : false;
        $element->days_expired = $request->expiration_date == "SI" ? $request->days_expired : NULL;
        $element->stock_minimun = $request->stock_minimun == "SI" ? true : false;
        $element->company_id = $this->company;
        $element->type = $types->implode(',');
        $element->mark = $mark->implode(',');
        $element->cost = $request->cost;
        
        if(!$element->save())
            return $this->respondHttp500();

        if ($request->image)
        {
            $file_tmp = $request->image;
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->getClientOriginalExtension();
            $file_tmp->storeAs($element->path_client(false), $nameFile, 's3');
            $element->image = $nameFile;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->data_sheet)
        {
            $file_tmp2 = $request->data_sheet;
            $nameFile2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp2->getClientOriginalExtension();
            $file_tmp2->storeAs($element->path_client(false), $nameFile2, 's3');
            $element->data_sheet = $nameFile2;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->user_manual)
        {
            $file_tmp3 = $request->user_manual;
            $nameFile3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp3->getClientOriginalExtension();
            $file_tmp3->storeAs($element->path_client(false), $nameFile3, 's3');
            $element->user_manual = $nameFile3;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->has('locations_stock') && count($request->locations_stock) > 0)
        {
            foreach ($request->locations_stock as $key => $stock) 
            {
                $record = new ElementStockMinimun;
                $record->element_id = $element->id;
                $record->location_id = $stock['id_loc'];
                $record->quantity = $stock['quantity'];
                $record->save();
            }
        }

        $this->saveLogActivitySystem('EPP - Elementos', 'Se creo el elemento '.$element->name.' ');

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
            $element->stock_minimun = $element->stock_minimun ? 'SI' : 'NO';
            if ($element->image)
                $element->path = $element->path_image();
            else
                $element->path = NULL;

            $locations = ElementStockMinimun::where('element_id', $element->id)->get();

            $stock_minimun = [];

            if ($locations->count() > 0)
            {
                foreach ($locations as $key => $loc) 
                {
                   $content = [
                    'id' => $loc->id,
                    'id_loc' => $loc->location_id,
                    'quantity' => $loc->quantity,
                    'multiselect_location' => $loc->location->multiselect(),
                   ];

                   array_push($stock_minimun, $content);
                }
            }

            $element->locations_stock = $stock_minimun;

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
            ],
            "data_sheet" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'application/pdf' && 
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')

                        $fail('Ficha Técnica debe ser pdf, Excel .xlsx o Word .docx');
                },
            ],
            "user_manual" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'application/pdf' && 
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                        $value->getClientMimeType() != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')

                        $fail('Manual de uso debe ser pdf, Excel .xlsx o Word .docx');
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
        $element->serial = $request->serial;
        $element->reference = $request->reference;
        $element->class_element = $request->class_element;
        $element->observations = $request->observations;
        $element->size = $request->size;
        $element->operating_instructions = $request->operating_instructions;
        $element->applicable_standard = $standar_apply->implode(',');
        $element->state = $request->state == "Activo" ? true : false;
        $element->reusable = $request->reusable == "SI" ? true : false;
        $element->identify_each_element = false;
        $element->expiration_date = $request->expiration_date == "SI" ? true : false;
        $element->days_expired = $request->expiration_date == "SI" ? $request->days_expired : NULL;
        $element->stock_minimun = $request->stock_minimun == "SI" ? true : false;
        $element->type = $types->implode(',');
        $element->mark = $mark->implode(',');
        $element->cost = $request->cost;
        
        if(!$element->update()){
          return $this->respondHttp500();
        }

        if ($request->image != $element->image)
        {
            $file_tmp = $request->image;
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->getClientOriginalExtension();
            $file_tmp->storeAs($element->path_client(false), $nameFile, 's3');
            $element->image = $nameFile;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->data_sheet != $element->data_sheet)
        {
            $file_tmp2 = $request->data_sheet;
            $nameFile2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp2->getClientOriginalExtension();
            $file_tmp2->storeAs($element->path_client(false), $nameFile2, 's3');
            $element->data_sheet = $nameFile2;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->user_manual != $element->user_manual)
        {
            $file_tmp3 = $request->user_manual;
            $nameFile3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp3->getClientOriginalExtension();
            $file_tmp3->storeAs($element->path_client(false), $nameFile3, 's3');
            $element->user_manual = $nameFile3;

            if(!$element->update())
                return $this->respondHttp500();
        }

        if ($request->has('locations_stock') && count($request->locations_stock) > 0)
        {
            foreach ($request->locations_stock as $key => $stock) 
            {
                $record = ElementStockMinimun::updateOrCreate(
                    [
                        "element_id" => $element->id,
                        "location_id" => $stock['id_loc']
                    ],
                    [
                        'element_id' => $element->id,
                        'location_id' => $stock['id_loc'],
                        'quantity' => $stock['quantity']
                    ]
                );
            }
        }

        $this->saveLogActivitySystem('EPP - Elementos', 'Se edito el elemento '.$element->name.' ');
        
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

        $this->saveLogActivitySystem('EPP - Elementos', 'Se elimino el elemento '.$element->name.' ');
        
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

    public function multiselectClassElement()
    {
        $class = Element::selectRaw("
                sau_epp_elements.class_element as name
            ")            
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($class);
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

    public function downloadDataSheet(Element $element)
    {
        $name = $element->data_sheet;

        return Storage::disk('s3')->download($element->path_donwload_data_shet(), $name);                                               
    }

    public function downloadUserManual(Element $element)
    {
        $name = $element->user_manual;

        return Storage::disk('s3')->download($element->path_donwload_user_manual(),$name);                                               
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

    public function importStockMinimun(Request $request)
    {
      try
      {
        ElementImportStockMinimunJob::dispatch($request->file, $this->company, $this->user);
      
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
            sau_epp_elements.class_element as class,
            count(sau_epp_elements_balance_specific.id) as quantity,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS quantity_available,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Asignado', 1, 0)) AS quantity_allocated,
            SUM(IF(sau_epp_elements_balance_specific.state = 'No Disponible', 1, 0)) AS quantity_transfer,
            SUM(IF(sau_epp_elements_balance_specific.state = 'No disponible o desechado', 1, 0)) AS quantity_wastes
        ")
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_locations.company_id', $this->company)
        ->groupBy('element','location', 'mark', 'class');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
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
            sau_epp_elements.class_element as class,
            count(sau_epp_elements_balance_specific.id) as cantidad,
            date_format(sau_epp_transactions_employees.created_at, '%Y-%m-%d') AS fecha
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
        ->groupBy('sau_employees.name', 'sau_epp_elements.id', 'sau_epp_elements.name','sau_epp_locations.name', 'sau_epp_elements.class_element', 'sau_epp_transactions_employees.created_at');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);
                
            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }

        
        return Vuetable::of($report)
                    ->make();
    }

    public function reportEmployeeTotals(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_employees.name as employee,
            count(sau_epp_elements_balance_specific.id) as asignados
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
        ->groupBy('sau_employees.name');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);

            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }

        return $this->respondHttp200([
            'data' => $report->get()
        ]);
    }

    public function reportTotal(Request $request)
    {
        $report = ElementBalanceLocation::selectRaw("
            count(sau_epp_elements_balance_specific.id) as total,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS disponibles,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Asignado', 1, 0)) AS asignados,
            SUM(IF(sau_epp_elements_balance_specific.state = 'No Disponible', 1, 0)) AS transito,
            SUM(IF(sau_epp_elements_balance_specific.state = 'No disponible o desechado', 1, 0)) AS desechados
        ")
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
        ->where('sau_epp_elements.company_id', $this->company);

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);
        }
        
        return $this->respondHttp200([
            'data' => $report->first()
        ]);
    }

    public function reportBalanceStockMinimun(Request $request)
    {
        $report = ElementBalanceLocation::selectRaw("
            sau_epp_elements.name as element,
            sau_epp_elements.mark as mark,
            sau_epp_locations.name as location,
            sau_epp_elements.class_element as class,
            sau_epp_minimum_stock.quantity as quantity,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS quantity_available
        ")
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
        ->join('sau_epp_minimum_stock', function ($join) 
        {
            $join->on("sau_epp_minimum_stock.element_id", 'sau_epp_elements_balance_ubication.element_id');
            $join->on("sau_epp_minimum_stock.location_id", 'sau_epp_elements_balance_ubication.location_id');
        })
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_minimum_stock.below_stock', true)
        ->groupBy('element','location', 'mark', 'class', 'quantity');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);
        }

        
        return Vuetable::of($report)
                    ->make();
    }

    public function reportEmployeeHistory(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_employees.name as employee,
            sau_epp_elements.id as id,
            sau_epp_elements.name as element,
            sau_epp_locations.name as location,
            sau_epp_elements.class_element as class,
            count(sau_epp_elements_balance_specific.id) as cantidad,
            date_format(sau_epp_transactions_employees.created_at, '%Y-%m-%d') AS fecha
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_specific.location_id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        //->whereNull('sau_epp_transactions_employees.state')
        ->groupBy('sau_employees.name', 'sau_epp_elements.id', 'sau_epp_elements.name','sau_epp_locations.name', 'sau_epp_elements.class_element', 'sau_epp_transactions_employees.created_at');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);

            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }

        
        return Vuetable::of($report)
                    ->make();
    }

    public function reportEmployeeTotalsHistory(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_employees.name as employee,
            count(sau_epp_elements_balance_specific.id) as asignados
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_specific.location_id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        //->whereNull('sau_epp_transactions_employees.state')
        ->groupBy('sau_employees.name');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);

            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }

        return $this->respondHttp200([
            'data' => $report->get()
        ]);
    }

    public function reportElementTop(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_epp_elements.name as category,
            count(sau_epp_elements_balance_specific.id) as count
        ")
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_specific.location_id')
        ->where('sau_epp_elements.company_id', $this->company)
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        //->whereNull('sau_epp_transactions_employees.state')
        ->groupBy('sau_epp_elements.name');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);

            if (isset($filters["regionals"]))
                $report->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $report->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $report->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);

            if (isset($filters["areas"]))
                $report->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            if (isset($filters["positions"]))
                $report->inPositions($this->getValuesForMultiselect($filters["positions"]), $filters['filtersType']['positions']);

            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }

        $report = $report->orderBy('count', 'DESC')
        ->limit(20)
        ->get()
        ->sortBy('count')
        ->pluck('count', 'category');

        return $this->buildDataChart($report);
    }

    protected function buildDataChart($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $label => $count) {
            $label2 = strlen($label) > 100 ? substr($this->sanear_string($label), 0, 50).'...' : $label;
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

    public function reportElementCost(Request $request)
    {
        $report = ElementTransactionEmployee::selectRaw("
            sau_epp_elements.id as id,
            sau_epp_elements.name as element,
            sau_epp_locations.name as location,
            sau_epp_locations.id as id_location,
            sau_epp_elements.cost as cost,
            count(sau_epp_elements_balance_specific.id) as cantidad,
            (sau_epp_elements.cost*count(sau_epp_elements_balance_specific.id)) as subtotal
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
        ->groupBy('sau_epp_elements.id', 'sau_epp_elements.name','sau_epp_locations.name', 'sau_epp_locations.id');

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

            if (isset($filters["class"]) && COUNT($filters["class"]) > 0)
            {
                $class = $this->getValuesForMultiselect($filters["class"]);

                if ($filters['filtersType']['class'] == 'IN')
                    $report->whereIn('sau_epp_elements.class_element', $class);

                else if ($filters['filtersType']['class'] == 'NOT IN')
                    $report->whereNotIn('sau_epp_elements.class_element', $class);
            }

            if (isset($filters["elements"]))
                $report->inElement($this->getValuesForMultiselect($filters["elements"]), $filters['filtersType']['elements']);

            if (isset($filters["location"]))
                $report->inLocation($this->getValuesForMultiselect($filters["location"]), $filters['filtersType']['location']);

            if (isset($filters["employee"]))
                $report->inEmployee($this->getValuesForMultiselect($filters["employee"]), $filters['filtersType']['employee']);
                
            if (isset($filters["dateRange"]))
            {
                $dates_request = explode('/', $filters["dateRange"]);
    
                $dates = [];
    
                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $report->betweenDate($dates);
            }
        }
        
        $report = $report->get()->groupBy('location');

        foreach ($report as $key => $location) 
        {
            $content = [
                'total_cantidad' => $location->sum('cantidad'),
                'total_cost' => $location->sum('cost'),
                'total' => $location->sum('subtotal')
            ];

            $location->push(['totals' => $content]);
        }


        return $report;
    }
}
