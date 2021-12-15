<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsRequest;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\FileTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\HashSelectDeliveryTemporal;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class TransactionController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:elements_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:elements_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:elements_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:elements_d, {$this->team}", ['only' => 'destroy']);*/
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
        $transactions = ElementTransactionEmployee::selectRaw(
            "sau_epp_transactions_employees.*,
            sau_employees.name AS employee"
        )        
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id');

        return Vuetable::of($transactions)
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\ElementRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementTransactionsRequest $request)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
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

        DB::beginTransaction();

        try
        {

            $employee = Employee::findOrFail($request->employee_id);

            $delivery = new ElementTransactionEmployee();
            $delivery->employee_id = $request->employee_id;
            $delivery->position_employee_id = $employee->position->id;
            $delivery->type = 'Entrega';
            $delivery->observations = $request->observations ? $request->observations : NULL;
            $delivery->location_id = $request->location_id;
            $delivery->company_id = $this->company;
            
            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);

                if ($element)
                {
                    if ($element->identify_each_element)
                    {
                        $disponible = ElementBalanceSpecific::where('hash', $value['code'])->where('location_id', $request->location_id)->first();

                        if ($disponible->state != 'Disponible')
                            return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        
                        $disponible->state = 'Asignado';
                        $disponible->save();

                        array_push($elements_sync, $disponible->id);

                        $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                        $element_balance->quantity_available = $element_balance->quantity_available - 1;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                        $element_balance->save();
                    }
                    else
                    {
                        $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();
                        \Log::info($value['quantity']);

                        $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity']);

                        \Log::info($disponible);

                        if (!$disponible)
                            return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        else if ($disponible->count() < $value['quantity'])
                            return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                        foreach ($disponible as $key => $value2) {
                            \Log::info($value2);
                            $value2->state = 'Asignado';
                            $value2->save();
                            array_push($elements_sync, $value2->id);
                        }

                        $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                        $element_balance->save();
                    }
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($request->firm_employee)
            {
                $image_64 = $request->firm_employee;
        
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        
                $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
        
                $image = str_replace($replace, '', $image_64); 
        
                $image = str_replace(' ', '+', $image); 
        
                $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                $file = base64_decode($image);

                //$file->storeAs($delivery->path_client(false), $imageName, 's3');

                Storage::disk('s3')->put('industrialSecure/epp/transaction/delivery/files/'.$this->company.'/' . $imageName, $file, '- ');

                $delivery->firm_employee = $imageName;

                if(!$delivery->update())
                    return $this->respondHttp500();
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la entrrga'
        ]);
    }

    public function processFiles($files, $transaction_id)
    {
        $files_names_delete = [];
    //\Log::info($files);
        foreach ($files as $keyF => $file) 
        {
            \Log::info($keyF);
            \Log::info($file);
            $create_file = true;

            if (isset($file['id']))
            {
                $fileUpload = FileTransactionEmployee::findOrFail($file['id']);

                if (isset($file['old_name']) && $file['old_name'] == $file['file'])
                    $create_file = false;
                else
                    array_push($files_names_delete, $file['old_name']);
            }
            else
            {
                $fileUpload = new FileTransactionEmployee();
                $fileUpload->transaction_employee_id = $transaction_id;
            }

            if ($create_file)
            {
                $file_tmp = $file['file'];
                \Log::info($file);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                $fileUpload->file = $nameFile;
            }

            if (!$fileUpload->save())
                return $this->respondHttp500();
        }

         //Borrar archivos reemplazados
         foreach ($files_names_delete as $keyf => $file)
         {
             Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
         }
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

    public function downloadImage(Element $element)
    {
        $name = $element->image;

        return Storage::disk('s3')->download($element->path_donwload(), $name);                                               
    }

    public function employeeInfo($id)
    {
        try
        {
            $employee = Employee::findOrFail($id);
            $employee->position_employee = $employee->position->name;

            $elements = [];

            foreach ($employee->position->elements as $key => $value)
            {   $content = [
                    'id_ele' => $value->id,
                    'quantity' => '',
                    'type' => $value->identify_each_element ? 'Identificable' : 'No Identificable',
                ];

                array_push($elements, $content);
            }

            $employee->elements = $elements;

            return $this->respondHttp200([
                'data' => $employee,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function elementsLocation(Request $request)
    {
        $multiselect = [];
        $elements_id = [];

        $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->where('location_id', $request->location_id)
        ->where('sau_epp_elements.company_id', $this->company)
        ->get()
        ->toArray();

        $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->where('sau_epp_elements_balance_specific.location_id', $request->location_id)
        ->where('sau_epp_elements_balance_specific.state', 'Disponible')
        ->whereIn('element_balance_id', $element_balance)
        ->get()
        ->toArray();

        $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->where('location_id', $request->location_id)
        ->where('sau_epp_elements.company_id', $this->company)
        ->get()
        ->toArray();

        $ids_disponibles = [];

        foreach ($element_disponibles as $key => $value) {
            $ele = Element::find($value['element_id']);

            array_push( $multiselect, $ele->multiselect());
            array_push( $ids_disponibles, $ele->id);
        }

        foreach ($request->position_elements as $key => $value) {

            if (in_array($value['id_ele'], $ids_disponibles))
            {
                $ele = Element::find($value['id_ele']);

                $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                ->where('element_id', $ele->id)
                ->first();

                $disponible = ElementBalanceSpecific::select('id', 'hash')
                ->where('location_id', $request->location_id)
                ->where('state', 'Disponible')
                ->where('element_balance_id', $element_balance->id)
                ->orderBy('id')
                ->pluck('hash', 'hash');

                $content = [
                    'id_ele' => $ele->id,
                    'quantity' => '',
                    'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                    'code' => ''
                ];

                $options = $this->multiSelectFormat($disponible);

                array_push( $elements_id, ['element' => $content, 'options' => $options]);
            }
        }

        $data = [
            'multiselect' => $multiselect,
            'elements' => $elements_id
        ];

        return $data;
    }

    public function elementInfo(Request $request)
    {
        $ele = Element::find($request->id);

        $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
        ->where('element_id', $request->id)
        ->first();

        $disponible = ElementBalanceSpecific::select('hash', 'hash')
        ->where('location_id', $request->location_id)
        ->where('state', 'Disponible')
        ->where('element_balance_id', $element_balance->id)
        ->get();

        foreach ($disponible as $key => $value) {
            $select = HashSelectDeliveryTemporal::where('hash', $value->hash)->exists();

            if ($select)
            {
                $disponible = $disponible->reject(function ($value2, $key) use ($value) {
                    return $value2->hash == $value->hash;
                });        
            }
        }

        $disponible = $disponible->pluck('hash', 'hash'); 
        
        if ($ele->identify_each_element)
        {
            return [
                'type' => 'Identificable',
                'options' => $this->multiSelectFormat($disponible)
            ];
        }
        else
        {
            return [
                'type' => 'No Identificable',
                'options' => []
            ];
        }
    }

    public function hashSelected(Request $request)
    {
        \Log::info($request);
        try
        { 
            $disponible = ElementBalanceSpecific::where('location_id', $request->location_id)
            ->where('state', 'Disponible')
            ->where('hash', $request->select_hash)
            ->first();

            if ($disponible)
            {
                $selected = new HashSelectDeliveryTemporal;
                $selected->element_id = $request->id;
                $selected->location_id = $request->location_id;
                $selected->hash = $disponible->hash;
                $selected->user_id = $this->user->id;
                $selected->save();
            }
            else
            {
                return $this->respondWithError('El código seleccionado no existe o no se encuentra disponible');
            }
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function deletedTemporal()
    {
        $delete = HashSelectDeliveryTemporal::where('user_id', $this->user->id)->delete();
    }
}
