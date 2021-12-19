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
use Carbon\Carbon;

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
            sau_employees.name AS employee,
            sau_employees_positions.name as position,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements"
        )        
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->groupBy('sau_epp_transactions_employees.id');

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

                        $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity'])->get();

                        if (!$disponible)
                            return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        else if ($disponible->count() < $value['quantity'])
                            return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                        foreach ($disponible as $key => $value2) {
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

                Storage::disk('s3')->put('industrialSecure/epp/transaction/delivery/files/'.$this->company.'/' . $imageName, $file, 'public');

                $delivery->firm_employee = $imageName;

                if(!$delivery->update())
                    return $this->respondHttp500();
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            $this->deletedTemporal();

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

        foreach ($files as $keyF => $file) 
        {
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
            $transaction = ElementTransactionEmployee::findOrFail($id);

            $transaction->multiselect_employee = $transaction->employee->multiselect();
            $transaction->position_employee = $transaction->position->name;
            $transaction->multiselect_location = $transaction->location->multiselect();

            $info = $this->employeeInfo($transaction->employee_id);

            $element_balance_id = [];
            
            foreach ($transaction->elements as $key => $value) 
            {
                if (!in_array($value->element_balance_id, $element_balance_id))
                {
                    array_push($element_balance_id, $value->element_balance_id);
                }
                
            }

            $multiselect = [];
            $elements_id = [];

            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $transaction->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $transaction->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $transaction->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();


            foreach ($element_disponibles as $key => $value) {
                $ele = Element::find($value['element_id']);

                array_push( $multiselect, $ele->multiselect());
            }

            $elements = [];
            $ids_balance_saltar = [];

            foreach ($element_balance_id as $key => $value) {
                $element = $transaction->elements()->where('element_balance_id', $value)->get();

                foreach ($element as $key => $e) 
                {
                    $disponible_hash = ElementBalanceSpecific::select('id', 'hash')
                    ->where('location_id', $transaction->location_id)
                    ->where('state', 'Disponible')
                    ->where('element_balance_id', $e->element_balance_id)
                    ->orderBy('id')
                    ->pluck('hash', 'hash');

                    $options = $this->multiSelectFormat($disponible_hash);

                    if ($e->element->element->identify_each_element)
                    {
                        $content = [
                            'id' => $e->id,
                            'id_ele' => $e->element->element->id,
                            'quantity' => '',
                            'type' => 'Identificable',
                            'code' => $e->hash,
                            'entry' => 'Manualmente',
                            'multiselect_element' => $e->element->element->multiselect()
                        ];

                        array_push($elements, ['element' => $content, 'options' => $options]);
                    }
                    else
                    {
                        if (!in_array($e->element_balance_id, $ids_balance_saltar))
                        {
                            $content = [
                                'id' => $e->id,
                                'id_ele' => $e->element->element->id,
                                'quantity' => $element->count(),
                                'type' => 'No Identificable',
                                'code' => '',
                                'entry' => 'Manualmente',
                                'multiselect_element' => $e->element->element->multiselect()
                            ];

                            array_push($elements, ['element' => $content, 'options' => $options]);
                            array_push($ids_balance_saltar, $e->element_balance_id);
                        }
                    }
                    
                }
            }

            $transaction->firm_image = $transaction->path_image();
            $transaction->old_firm = $transaction->firm_employee;

            $transaction->elements_codes = $elements;

            $transaction->elements_id = [];
            $transaction->elementos = $multiselect;

            $transaction->element = [
                'multiselect' => $multiselect,
                'element' => $elements
            ];

            $transaction->files = $this->getFiles($transaction->id);

            $transaction->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $transaction,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function getFiles($transaction)
    {
        $get_files = FileTransactionEmployee::where('transaction_employee_id', $transaction)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->id = $get_file->id;
                $get_file->name = $get_file->file;
                $get_file->old_name = $get_file->file;
                $get_file->path = $get_file->path_image();

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\elements\ElementTransactionsRequest  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
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

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Entrega';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            
            if(!$transaction->update()){
            return $this->respondHttp500();
            }

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                if (isset($value['id']))
                {
                    $disponible = ElementBalanceSpecific::find($value['id']);
                    $element = Element::find($value['id_ele']);

                    if ($value['type'] == 'Identificable')
                    {
                        if ($disponible->hash != $value['code'])
                        {
                            $disponible->state = 'Disponible';
                            $disponible->save();
                            $new = ElementBalanceSpecific::where('hash', $value['code'])->first();
                            $new->state = 'Asignado';
                            $new->save();

                            array_push($elements_sync, $new->id);
                        }
                        else
                            array_push($elements_sync, $disponible->id);
                    }
                    else
                    {

                        $trans = ElementBalanceSpecific::join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                        ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                        ->where('sau_epp_elements_balance_specific.id', $value['id'])
                        ->first();
                            
                        $transac_id = $trans->transaction_employee_id;
                        $id_balance = $trans->element_balance_id;

                        $elements = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                        ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
                        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                        ->where('sau_epp_transactions_employees.id', $transac_id)
                        ->where('sau_epp_elements.identify_each_element', false)
                        ->get();

                        if ($value['quantity'] > $elements->count())
                        {
                            $count = $value['quantity'] - $elements->count();

                            $elements_news = ElementBalanceSpecific::where('element_balance_id', $disponible->element_balance_id)
                            ->where('state', 'Disponible')
                            ->where('location_id', $transaction->location_id)
                            ->limit($count)->get();

                            if ($elements_news->count() < $count)
                                return $this->respondWithError('El elemento ' . $element->name . ' no cuenta con suficientes unidades disponibles');

                            foreach ($elements_news as $key => $value2) {
                                $value2->state = 'Asignado';
                                $value2->save();
                                array_push($elements_sync, $value2->id);
                            }

                            foreach ($elements as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }

                            $element_balance = ElementBalanceLocation::find($id_balance);

                            $element_balance->quantity_available = $element_balance->quantity_available - $count;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + $count;

                        }
                        else if ($value['quantity'] < $elements->count())
                        {
                            $count = $elements->count() - $value['quantity'];

                            $elements_delete = $elements->take($count)->all();

                            foreach ($elements_delete as $key => $value2) {
                                $value2->state = 'Disponible';
                                $value2->save();
                            }

                            $elements_restantes = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                            ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                            ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                            ->where('sau_epp_transactions_employees.id', $transac_id)
                            ->where('sau_epp_elements_balance_specific.state', 'Asignado')
                            ->get();

                            foreach ($elements_restantes as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }

                            $element_balance = ElementBalanceLocation::find($id_balance);

                            $element_balance->quantity_available = $element_balance->quantity_available - $count;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + $count;
                        }
                        else
                        {
                            foreach ($elements as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }
                        }
                    }
                }
                else
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

                            $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity'])->get();

                            if (!$disponible)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            else if ($disponible->count() < $value['quantity'])
                                return $this->respondWithError('El elemento ' . $element->name . '  no cuenta con suficientes unidades disponibles');

                            foreach ($disponible as $key => $value2) {
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
            }

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/delivery/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/delivery/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transaction->firm_employee = $imageName;

                    if(!$transaction->update())
                        return $this->respondHttp500();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transaction->id);
            }

            $this->deletedTemporal();
            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la transacción'
        ]);
    }

    public function deleteData($delete)
    {
        foreach ($delete['files'] as $id)
        {
            $file_delete = FileTransactionEmployee::find($id);

            if ($file_delete)
            {
                $path = $file_delete->file;
                $file_delete->delete();
                Storage::disk('s3')->delete('industrialSecure/epp/transaction/delivery/files/' . $this->company . '/' . $path);
            }
        }

        foreach ($delete['elements'] as $id)
        {
            $element = ElementBalanceSpecific::find($id);

            if ($element)
            {
                $element->state = 'Disponible';
                $element->save();
            }
                
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(ElementTransactionEmployee $transaction)
    {
        Storage::disk('s3')->delete($transaction->path_client(false)."/".$transaction->firm_employee);

        if(!$element->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el peligro'
        ]);
    }

    public function download(FileTransactionEmployee $file)
    {
      return Storage::disk('s3')->download($file->path_donwload());
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
        try
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
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
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
