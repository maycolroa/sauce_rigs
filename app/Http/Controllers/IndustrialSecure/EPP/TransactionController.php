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
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsReturnsRequest;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\FileTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\HashSelectDeliveryTemporal;
use App\Models\IndustrialSecure\Epp\EppWastes;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\Company;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;
use PDF;
use App\Models\Administrative\Users\User;

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
        ->where('sau_epp_transactions_employees.type', 'Entrega')
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
        if ($request->type == 'Entrega')
        {
            if ($request->inventary == 'SI')
                return $this->storeDelivery($request);
            else
                return $this->storeDeliveryNotInventary($request);
        }
        else
        {
            return $this->storeReturns($request);
        }

    }

    public function storeDelivery(ElementTransactionsRequest $request)
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
            $delivery->edit_firm = $request->edit_firm;
            $delivery->firm_email = $request->firm_email;
            $delivery->email_firm_employee = $request->email_firm_employee;
            $delivery->user_id = $this->user->id;
            
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

            if ($delivery->edit_firm)
            {
                if ($request->firm_employee && $delivery->firm_email == 'Dibujar')
                {
                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $delivery->firm_employee = $imageName;

                    if(!$delivery->update())
                        return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        //->with(['user' => $employee->name, 'urls'=>$url_email])
                        //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                        ->company($this->company)
                        ->send();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            $this->deletedTemporal();

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se creo la entrega'
            ]);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function storeDeliveryNotInventary(ElementTransactionsRequest $request)
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
            $delivery->edit_firm = $request->edit_firm;
            $delivery->firm_email = $request->firm_email;
            $delivery->email_firm_employee = $request->email_firm_employee;
            
            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);

                if ($element)
                {
                    $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                    for ($i=1; $i <= $value['quantity']; $i++) { 
                        $hash = Hash::make($element_balance->element_id . str_random(30));
                        $product = new ElementBalanceSpecific;
                        $product->hash = $hash;
                        $product->code = $hash;
                        $product->element_balance_id = $element_balance->id;
                        $product->location_id = $element_balance->location_id;
                        $product->state = 'Asignado';
                        $product->save();

                        array_push($elements_sync, $product->id);
                    }

                    $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                    $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                    $element_balance->save();
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($delivery->edit_firm)
            {
                if ($request->firm_employee && $delivery->firm_email == 'Dibujar')
                {
                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $delivery->firm_employee = $imageName;

                    if(!$delivery->update())
                        return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        //->with(['user' => $employee->name, 'urls'=>$url_email])
                        //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                        ->company($this->company)
                        ->send();
                }
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
            'message' => 'Se creo la entrega'
        ]);
    }

    public function storeReturns(ElementTransactionsRequest $request)
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

                Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

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

            $multiselect = [];
            $elements_id = [];
            $elements = [];

            if ($transaction->type == 'Entrega')
            {
                $info = $this->employeeInfo($transaction->employee_id);

                $element_balance_id = [];
                
                foreach ($transaction->elements as $key => $value) 
                {
                    if (!in_array($value->element_balance_id, $element_balance_id))
                    {
                        array_push($element_balance_id, $value->element_balance_id);
                    }                    
                }

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
            }
            else
            {
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

            $transaction->inventary = '';

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

        if ($transaction->type == 'Entrega')
        {
            if ($request->inventary == 'SI')
                return $this->updateDelivery($request, $transaction);
            else
                return $this->updateDeliveryNotInventary($request, $transaction);
        }
        else
        {
            return $this->updateReturn($request, $transaction);
        }

    }

    public function updateDelivery(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    { 
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Entrega';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            $transaction->edit_firm = $request->edit_firm;
            $transaction->firm_email = $request->firm_email;
            $transaction->email_firm_employee = $request->email_firm_employee;
            
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
                        ->where('sau_epp_elements_balance_specific.element_balance_id', $id_balance)
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
                            
                            $element_balance->save();

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

                            $element_balance->quantity_available = $element_balance->quantity_available + $count;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated - $count;

                            $element_balance->save();
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
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

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

    public function updateDeliveryNotInventary(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    { 
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Entrega';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            $transaction->edit_firm = $request->edit_firm;
            $transaction->firm_email = $request->firm_email;
            $transaction->email_firm_employee = $request->email_firm_employee;
            
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
                    ->where('sau_epp_elements_balance_specific.element_balance_id', $id_balance)
                    ->where('sau_epp_elements.identify_each_element', false)
                    ->get();

                    if ($value['quantity'] > $elements->count())
                    {
                        $count = $value['quantity'] - $elements->count();

                        for ($i=1; $i <= $count; $i++) { 
                            $hash = Hash::make($id_balance . str_random(30));
                            $product = new ElementBalanceSpecific;
                            $product->hash = $hash;
                            $product->code = $hash;
                            $product->element_balance_id = $id_balance;
                            $product->location_id = $request->location_id;
                            $product->state = 'Asignado';
                            $product->save();
    
                            array_push($elements_sync, $product->id);
                        }

                        foreach ($elements as $key => $value) {
                            array_push($elements_sync, $value->id);
                        }

                        $element_balance = ElementBalanceLocation::find($id_balance);

                        $element_balance->quantity_available = $element_balance->quantity_available - $count;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + $count;
                        
                        $element_balance->save();

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

                        $element_balance->quantity_available = $element_balance->quantity_available + $count;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated - $count;

                        $element_balance->save();
                    }
                    else
                    {
                        foreach ($elements as $key => $value) {
                            array_push($elements_sync, $value->id);
                        }
                    }
                }
                else
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                    for ($i=1; $i <= $value['quantity']; $i++) { 
                        $hash = Hash::make($element_balance->element_id . str_random(30));
                        $product = new ElementBalanceSpecific;
                        $product->hash = $hash;
                        $product->code = $hash;
                        $product->element_balance_id = $element_balance->id;
                        $product->location_id = $element_balance->location_id;
                        $product->state = 'Asignado';
                        $product->save();

                        array_push($elements_sync, $product->id);
                    }

                    $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                    $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                    $element_balance->save();
                    }
                }
            }

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

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

    public function updateReturn(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    {
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Devolucion';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            
            if(!$transaction->update()){
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

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transaction->firm_employee = $imageName;

                    if(!$transaction->update())
                        return $this->respondHttp500();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transaction->id);
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
                Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/' . $this->company . '/' . $path);
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

        $get_files = FileTransactionEmployee::where('transaction_employee_id', $transaction->id)->get();

        foreach ($get_files as $key => $value) {
            Storage::disk('s3')->delete($value->path_client(false)."/".$value->file);
        }

        if ($transaction->type == 'Entrega')
        {
            foreach ($transaction->elements as $key => $value) 
            {
                if ($value->state == 'Desechado')
                    continue;

                $value->state = 'Disponible';
                $value->save();

                $element_balance = ElementBalanceLocation::find($value->element_balance_id);

                $element_balance->quantity_available = $element_balance->quantity_available + 1;

                $element_balance->quantity_allocated = $element_balance->quantity_allocated - 1;

                $element_balance->save();
                
            }
        }
        else
        {
            foreach ($transaction->elements as $key => $value) 
            {
                if ($value->state == 'Desechado')
                    continue;

                $value->state = 'Asignado';
                $value->save();

                /*$desecho = EppWastes::where('code_element', $value->hash)
                ->where('employee_id', $value->employee_id)->first();

                if ($desecho)
                    $desecho->delete();*/

                $element_balance = ElementBalanceLocation::find($value->element_balance_id);

                $element_balance->quantity_available = $element_balance->quantity_available - 1;

                $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                $element_balance->save();
                
            }
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
            $elements_no_disponibles = [];

            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            if ($request->inventary == 'SI')
            {
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
                    else
                    {
                        $ele = Element::find($value['id_ele']);
                        array_push($elements_no_disponibles, $ele->name);
                    }
                }
            }
            else
            {
                $element_disponibles = ElementBalanceLocation::select('element_id')
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

                        $content = [
                            'id_ele' => $ele->id,
                            'quantity' => '',
                            'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                            'code' => ''
                        ];

                        array_push( $elements_id, ['element' => $content, 'options' => []]);
                    }
                }
            }

            $data = [
                'multiselect' => $multiselect,
                'elements' => $elements_id,
                'elements_no_disponibles' => $elements_no_disponibles
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

        if ($ele->identify_each_element)
        {
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
        }
        
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

    public function downloadPdf($id)
    {
        $delivery = $this->getDataExportPdf($id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.letterDeliveryEpp', ['delivery' => $delivery] );

        $pdf->setPaper('A4');

        return $pdf->download('Entrega EPP.pdf');
    }

    public function getDataExportPdf($id)
    {
        $delivery = ElementTransactionEmployee::findOrFail($id);

        $delivery->employee_name = $delivery->employee->name;
        $delivery->employee_identification = $delivery->employee->identification;

        $element_balance_id = [];
        $elements = [];
            
        foreach ($delivery->elements as $key => $value) 
        {
            if (!in_array($value->element_balance_id, $element_balance_id))
            {
                array_push($element_balance_id, $value->element_balance_id);
            }                    
        }

        $ids_balance_saltar = [];

        foreach ($element_balance_id as $key => $value) 
        {
            $element = $delivery->elements()->where('element_balance_id', $value)->get();

            foreach ($element as $key => $e) 
            {
                if ($e->element->element->identify_each_element)
                {
                    $content = [
                        'quantity' => 1,
                        'name' => $e->element->element->name
                    ];

                    array_push($elements, $content);
                }
                else
                {
                    if (!in_array($e->element_balance_id, $ids_balance_saltar))
                    {
                        $content = [
                            'quantity' => $element->count(),
                            'name' => $e->element->element->name
                        ];

                        array_push($elements, $content);
                        array_push($ids_balance_saltar, $e->element_balance_id);
                    }
                }
                
            }
        }

        if ($delivery->firm_employee)
            $delivery->firm = $delivery->path_image();
        else
            $delivery->firm = NULL;

        $delivery->elements = $elements;
        $delivery->user_name = $delivery->user_id ? $delivery->user->name : '';

        $company = Company::select('logo', 'name')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $delivery->logo = $logo;

        $delivery->text_company = $this->getTextLetterEpp($company->name);

        return $delivery;
    }

    public function getTextLetterEpp($company)
    {
        $text = ConfigurationCompany::select('value')->where('key', 'text_letter_epp')->first();

        $text_default = '<p>Yo, empleado (a) de '.$company .' hago constar que he recibido lo aquí relacionado y firmado por mí.  Doy fé además que he sido informado y capacitado en cuanto al uso de los elementos de protección personal y  frente a los riesgos que me protegen, las actividades y ocasiones en las cuales debo utilizarlos.  He sido informado sobre el procedimiento para su cambio y reposición en caso que sea necesario.

        *Me comprometo a hacer buen uso de todo lo recibido y a realizar el mantenimiento adecuado de los mismos.   Me comprometo a utilizarlos y cuidarlos conforme a las instrucciones recibidas y a la normativa legal vigente; así mismo me comprometo a informar a mi jefe inmediato cualquier defecto, anomalía o daño del elemento de protección personal (EPP) que pueda afectar o disminuir la efectividad de la protección.</p>';

        if (!$text)
            return $text_default;
        else
            return $text->value;
    }
}
