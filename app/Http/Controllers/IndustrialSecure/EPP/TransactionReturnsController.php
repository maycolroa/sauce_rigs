<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsReturnsRequest;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\FileTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\HashSelectDeliveryTemporal;
use App\Models\IndustrialSecure\Epp\EppWastes;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;

class TransactionReturnsController extends Controller
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
        ->where('sau_epp_transactions_employees.type', 'Devolucion')
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
    public function store(ElementTransactionsReturnsRequest $request)
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

            $returns = new ElementTransactionEmployee();
            $returns->employee_id = $request->employee_id;
            $returns->position_employee_id = $employee->position->id;
            $returns->type = 'Devolucion';
            $returns->observations = $request->observations ? $request->observations : NULL;
            $returns->location_id = $request->location_id;
            $returns->company_id = $this->company;
            
            if(!$returns->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);

                if ($element)
                {
                    $asignado = ElementBalanceSpecific::where('hash', $value['code'])->where('location_id', $request->location_id)->first();

                    if ($asignado->state != 'Asignado')
                        return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra asignado en la ubicación seleccionada');

                    if ($value['waste'] == 'NO')
                    {                    
                        $asignado->state = 'Disponible';
                        $asignado->save();

                        array_push($elements_sync, $asignado->id);

                        $element_balance = ElementBalanceLocation::find($asignado->element_balance_id);

                        $element_balance->quantity_available = $element_balance->quantity_available + 1;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated - 1;

                        $element_balance->save();
                    }
                    else
                    {
                        $asignado->state = 'Desechado';
                        $asignado->save();

                        $desecho = new EppWastes;
                        $desecho->company_id = $this->company;
                        $desecho->employee_id = $request->employee_id;
                        $desecho->element_id = $asignado->id;
                        $desecho->location_id = $request->location_id;
                        $desecho->code_element = $value['code'];
                        $desecho->save();

                        array_push($elements_sync, $asignado->id);

                        $element_balance = ElementBalanceLocation::find($asignado->element_balance_id);

                        $element_balance->quantity_available = $element_balance->quantity_available - 1;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated - 1;

                        $element_balance->save();
                    }
                }
            }

            $returns->elements()->sync($elements_sync);

            if ($request->firm_employee)
            {
                $image_64 = $request->firm_employee;
        
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        
                $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
        
                $image = str_replace($replace, '', $image_64); 
        
                $image = str_replace(' ', '+', $image); 
        
                $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                $file = base64_decode($image);

                Storage::disk('s3')->put('industrialSecure/epp/transaction/return/files/'.$this->company.'/' . $imageName, $file, 'public');

                $returns->firm_employee = $imageName;


                if(!$returns->update())
                    return $this->respondHttp500();
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $returns->id);
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la devolución'
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
                $file_tmp->storeAs($fileUpload->path_client_return(false), $nameFile, 's3');
                $fileUpload->file = $nameFile;
            }

            if (!$fileUpload->save())
                return $this->respondHttp500();
        }

         //Borrar archivos reemplazados
         foreach ($files_names_delete as $keyf => $file)
         {
             Storage::disk('s3')->delete($fileUpload->path_client_return(false)."/".$file);
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

            $multiselect = [];
            $elements = [];

            foreach ($transaction->elements as $key => $value)
            {   
                $element = ElementBalanceSpecific::select('state')
                ->where('location_id', $transaction->location_id)
                ->where('id', $value->id)
                ->where('element_balance_id', $value->element_balance_id)
                ->first();

                if ($value->element->element->identify_each_element)
                {
                    $content = [
                        'id' => $value->id,
                        'id_ele' => $value->element->element->id,
                        'type' => 'Identificable',
                        'quantity' => 1,
                        'code' => $value->hash,
                        'multiselect_element' => $value->element->element->multiselect(),
                        'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                        'wastes' => $element->state == 'Desechado' ? 'SI' : 'NO'
                    ];

                    array_push($elements, $content);
                    array_push( $multiselect, $value->element->element->multiselect());
                }
                else
                {
                    $content = [
                        'id' => $value->id,
                        'id_ele' => $value->element->element->id,
                        'quantity' => 1,
                        'type' => 'No Identificable',
                        'code' => $value->hash,
                        'multiselect_element' => $value->element->element->multiselect(),
                        'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                        'wastes' => $element->state == 'Desechado' ? 'SI' : 'NO'
                    ];

                    array_push($elements, $content);
                    array_push( $multiselect, $value->element->element->multiselect());
                }                                
            }

            $transaction->firm_image = $transaction->path_image_return();
            $transaction->old_firm = $transaction->firm_employee;

            if ($transaction->firm_employee)
                $transaction->edit_firm == 'SI';

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
     * @param  App\Http\Requests\IndustrialSecure\elements\ElementTransactionsReturnsRequest  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(ElementTransactionsReturnsRequest $request, ElementTransactionEmployee $transactions)
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

            $transactions->employee_id = $request->employee_id;
            $transactions->position_employee_id = $employee->position->id;
            $transactions->type = 'Devolucion';
            $transactions->observations = $request->observations ? $request->observations : NULL;
            $transactions->location_id = $request->location_id;
            
            if(!$transactions->update()){
                return $this->respondHttp500();
            }

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $disponible = ElementBalanceSpecific::find($value['id']);
                $element = Element::find($value['id_ele']);
                $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                if ($disponible->hash == $value['code'])
                {
                    if ($disponible->state == 'Desechado')
                    {
                        if ($value['waste'] == 'NO')
                        {
                            $disponible->state = 'Disponible';
                            $disponible->save();

                            $desecho = EppWastes::where('code_element', $value['code'])
                            ->where('employee_id', $request->employee_id)->first();

                            if ($desecho)
                                $desecho->delete();

                            $element_balance->quantity_available = $element_balance->quantity_available + 1;
                            $element_balance->save();
                        }
                    }
                    else
                    {
                        if ($value['waste'] == 'SI')
                        {
                            $disponible->state = 'Desechado';
                            $disponible->save();

                            $desecho = new EppWastes;
                            $desecho->company_id = $this->company;
                            $desecho->employee_id = $request->employee_id;
                            $desecho->element_id = $disponible->id;
                            $desecho->location_id = $request->location_id;
                            $desecho->code_element = $value['code'];
                            $desecho->save();

                            $element_balance->quantity_available = $element_balance->quantity_available - 1;
                            $element_balance->save();
                        }
                    }
                    
                    array_push($elements_sync, $disponible->id);
                }
            }

            $transactions->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $transactions->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transactions->firm_employee = $imageName;

                    if(!$transactions->update())
                        return $this->respondHttp500();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transactions->id);
            }

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la devolución'
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
                Storage::disk('s3')->delete('industrialSecure/epp/transaction/returns/files/' . $this->company . '/' . $path);
            }
        }

        foreach ($delete['elements'] as $id)
        {
            $element = ElementBalanceSpecific::find($id);

            if ($element)
            {
                $element->state = 'Asignado';
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
    public function destroy(ElementTransactionEmployee $transactions)
    {
        Storage::disk('s3')->delete($transactions->path_client_return(false)."/".$transactions->firm_employee);

        $get_files = FileTransactionEmployee::where('transaction_employee_id', $transactions->id)->get();

        foreach ($get_files as $key => $value) {
            Storage::disk('s3')->delete($value->path_client_return(false)."/".$value->file);
        }

        foreach ($transactions->elements as $key => $value) 
        {
            $value->state = 'Asignado';
            $value->save();

            $desecho = EppWastes::where('code_element', $value->hash)
            ->where('employee_id', $value->employee_id)->first();

            if ($desecho)
                $desecho->delete();

            $element_balance = ElementBalanceLocation::find($value->element_balance_id);

            $element_balance->quantity_available = $element_balance->quantity_available - 1;

            $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

            $element_balance->save();
            
        }

        if(!$transaction->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la transacción'
        ]);
    }

    public function download(FileTransactionEmployee $file)
    {
      return Storage::disk('s3')->download($file->path_donwload_return());
    }

    public function employeeInfo($id)
    {
        try
        {
            $employee = Employee::findOrFail($id);
            $employee->position_employee = $employee->position->name;

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
            $transactions = ElementTransactionEmployee::where('employee_id', $request->employee_id)
            //->where('location_id', $request->location_id)
            ->where('type', 'Entrega')
            ->WhereNull('state')
            ->get();
            
            $elements = collect([]);                  
            $multiselect = [];
            $id_balance = [];
            $ids_transactions = [];

            if ($transactions->count() > 0)
            { 
                foreach ($transactions as $key => $transaction) 
                {                    
                    array_push($ids_transactions, $transaction->id);

                    foreach ($transaction->elements as $key => $value)
                    {
                        if ($value->state == 'Asignado')
                        {
                            if (!in_array($value->element_balance_id, $id_balance))
                                array_push($id_balance, $value->element_balance_id);

                            if ($value->element->element->identify_each_element)
                            {
                                $content = [
                                    'id' => $value->id,
                                    'id_ele' => $value->element->element->id,
                                    'balance_id' => $value->element_balance_id,
                                    'type' => 'Identificable',
                                    'quantity' => 1,
                                    'code' => $value->hash,
                                    'multiselect_element' => $value->element->element->multiselect(),
                                    'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                                    'wastes' => 'NO'
                                ];
        
                                $elements->push($content);
                                array_push($multiselect, $value->element->element->multiselect());
                            }
                            else
                            {
                                $content = [
                                    'id' => $value->id,
                                    'id_ele' => $value->element->element->id,
                                    'balance_id' => $value->element_balance_id,
                                    'quantity' => 1,
                                    'type' => 'No Identificable',
                                    'code' => $value->hash,
                                    'multiselect_element' => $value->element->element->multiselect(),
                                    'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                                    'wastes' => 'NO'
                                ];

                                //array_push($elements, $content);
                                $elements->push($content);
                                array_push( $multiselect, $value->element->element->multiselect());
                            }
                        }                                
                    }
                }
            }

            $ids_saltar = [];
            $new = [];

            foreach ($id_balance as $key => $id) 
            {
                $record = $elements->where('balance_id', $id);

                $cantidad = $elements->where('balance_id', $id)->where('type', 'No Identificable')->sum('quantity');
                $codes = [];

                foreach ($record as $key => $hash) 
                {
                    array_push($codes, $hash['code']);
                }

                foreach ($record as $key => $value) 
                {
                    if ($value['type'] == 'Identificable')
                    {
                        $disponible = ElementBalanceSpecific::select('id', 'hash')
                                ->where('location_id', $request->location_id)
                                ->where('state', 'Disponible')
                                ->where('element_balance_id', $value['balance_id'])
                                ->orderBy('id')
                                ->pluck('hash', 'hash');

                        $content = [
                            'id' => $value['id'],
                            'id_ele' => $value['id_ele'],
                            'quantity' => 1,
                            'type' => $value['type'],
                            'code' => $value['code'],
                            'multiselect_element' => $value['multiselect_element'],
                            'key' => $value['key'],
                            'wastes' => 'NO',
                            'rechange' => 'NO'
                        ];

                        $options = $this->multiSelectFormat($disponible);

                        //$elements->push(['element' => $content, 'options' => $options]);
                        array_push($new, ['element' => $content, 'options' => $options]);
                    }
                    else
                    {
                        if (!in_array($value['balance_id'], $ids_saltar))
                        {
                            $content = [
                                'id' => $value['id'],
                                'id_ele' => $value['id_ele'],
                                'quantity' => $cantidad,
                                'type' => $value['type'],
                                'code' => implode(',', $codes),
                                'multiselect_element' => $value['multiselect_element'],
                                'key' => $value['key'],
                                'wastes' => 'NO',
                                'rechange' => 'NO'
                            ];

                            array_push($ids_saltar, $value['balance_id']);
                            array_push($new, ['element' => $content, 'options' => []]);
                        }
                    }
                }
            }

            return $this->respondHttp200([
                'data' => [
                    'elements' => $new,
                    'multiselect' => $multiselect,
                    'id_transactions' => $ids_transactions
                ]
            ]);

        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function dataWastes(Request $request)
    {
        $histories = EppWastes::select(
            'sau_epp_wastes.*',
            'sau_epp_elements.name as name_element',
            'sau_epp_locations.name as name_location'
        )
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_wastes.element_id')
        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_specific.location_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_epp_wastes.employee_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
